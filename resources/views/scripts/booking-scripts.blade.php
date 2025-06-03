<script>
    const timeSlotCache = new Map();
    const STORE_CLOSING_TIME = '20:00';
    $(document).ready(function () {
        const serviceSelect = document.getElementById('serviceSelect');
        const durationText = document.getElementById('durationText');
        const noteToggle = document.getElementById('noteToggle');
        const noteGroup = document.getElementById('noteGroup');
        const timeSlotsContainer = $('#timeSlots');



        function debounce(func, wait) {
            let timeOut;
            return function excutedFunction(...args) {
                const later = () => {
                    clearTimeout(timeOut);
                    func(...args);
                };
                clearTimeout(timeOut);
                timeOut = setTimeout(later, wait);
            }
        }

        const showLoading = () => {
            timeSlotsContainer.html('<div class="text-center"><div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div> Đang tải khung giờ...</div>');
        };

        // Toggle ghi chú
        noteToggle.addEventListener('change', () => {
            noteGroup.style.display = noteToggle.checked ? 'block' : 'none';
        });

        // Load dịch vụ
        $.ajax({
            url: 'api/services/getAll',
            method: 'GET',
            success: function (res) {
                if (res.success && res.data.length > 0) {
                    res.data.forEach(service => {
                        const option = document.createElement('option');
                        option.value = service.id;
                        option.textContent = `${service.service_name}`;
                        option.dataset.duration = service.duration;
                        serviceSelect.appendChild(option);
                    });
                }
            }
        });

        // Hiển thị thời gian thực hiện
        serviceSelect.addEventListener('change', function () {
            resetTimeSlots();
            const selected = this.options[this.selectedIndex];
            const duration = selected.dataset.duration;

            if (duration) {
                console.log('SERVICE ' + duration);
                durationText.style.display = 'block';
                durationText.textContent = `Thời gian thực hiện: ${duration} phút.`;

                updateTimeSlotsBasedOnDuration(parseInt(duration));
            } else {
                durationText.style.display = 'none';

                resetTimeSlots();
            }
        });

        const generateTimeSlots = () => {
            const selectedDate = document.getElementById('bookingDate').value;


            const today = new Date();
            const selectedDateValue = new Date(selectedDate);
            const twoWeeksLater = new Date();
            twoWeeksLater.setDate(today.getDate() + 14);

            today.setHours(0, 0, 0, 0);
            selectedDateValue.setHours(0, 0, 0, 0);
            twoWeeksLater.setHours(0, 0, 0, 0);

            let errors = [];
            if (selectedDateValue < today) {
                errors.push('Không thể đặt lịch trong quá khứ');
            } else if (selectedDateValue > twoWeeksLater) {
                errors.push('Chỉ có thể đặt lịch trước 14 ngày');
            }

            if (errors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: errors.join('\n')
                });
                return;
            }


            console.log('Generating time slots for:', selectedDate);
            console.log('Cache has date:', timeSlotCache.has(selectedDate));
            console.log('Cache size:', timeSlotCache.size);

            if (timeSlotCache.has(selectedDate)) {
                console.log('Using cached data for:', selectedDate);
                renderTimeSlots(timeSlotCache.get(selectedDate));
                return;
            }

            showLoading();

            $.ajax({
                url: 'api/timeslots/generate-slot',
                method: 'POST',
                dataType: 'json',
                data: {
                    date: selectedDate
                },
                success: function (res) {
                    console.log('API Response:', res);

                    timeSlotCache.set(selectedDate, res);
                    console.log('Data cached for:', selectedDate);
                    console.log('Cache size after set:', timeSlotCache.size);

                    renderTimeSlots(res);
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching time slots:', error);
                    timeSlotsContainer.html('<div class="text-center text-danger">Lỗi khi tải khung giờ. Vui lòng thử lại.</div>');
                }
            });


        };

        timeSlotsContainer.on('click', '.time-btn:not(:disabled)', function () {
            timeSlotsContainer.find('.time-btn').removeClass('active');
            $(this).addClass('active');
            console.log('Selected time:', $(this).data('time')); // Debug
        });

        const renderTimeSlots = (res) => {
            timeSlotsContainer.html('');
            if (res.success && res.data && res.data.length > 0) {

                const selectedService = serviceSelect.options[serviceSelect.selectedIndex];
                const serviceDuration = selectedService ? parseInt(selectedService.dataset.duration) : null;

                res.data.forEach(slot => {
                    const col = $('<div>').addClass('col-3 mb-2');
                    const button = $('<button>')
                        .attr('type', 'button')
                        .addClass('btn btn-outline-secondary w-100 time-btn')
                        .attr('data-time', slot.time)
                        .prop('disabled', slot.disabled)
                        .text(slot.time);


                    let isDisabled = slot.disabled;
                    let disabledReason = slot.disabled ? 'Đã có lịch hẹn': '';

                    if (!isDisabled && serviceDuration && !isTimeSlotValid(slot.time, serviceDuration)) {
                        isDisabled = true;
                        disabledReason = `Không đủ thời gian (cần ${serviceDuration}p, cửa hàng đóng ${STORE_CLOSING_TIME})`;
                        button.addClass('insufficient-time');
                    };

                    button.prop('disabled', isDisabled);

                    if (disabledReason) {
                        button.attr('title', disabledReason);
                    };

                    col.append(button);
                    timeSlotsContainer.append(col);

                });
            } else {
                timeSlotsContainer.html('<div class="text-center text-danger">Không có khung giờ khả dụng.</div>');
            }
        };

        const invalidateCache = (date = null) => {
            if (date) {
                timeSlotCache.delete(date);
                console.log('Cache invalidated for:', date);
            } else {
                timeSlotCache.clear();
                console.log('All cache cleared');
            }
        };

        const debouncedGenerateTimeSlots = debounce(generateTimeSlots, 300);

        document.getElementById('bookingDate').addEventListener('change', debouncedGenerateTimeSlots);

        $('#bookingForm').on('submit', function (e) {
            e.preventDefault();

            const customerId = $('#user-name').data('id');
            const bookingDate = $('#bookingDate').val();
            const serviceId = $('#serviceSelect').val();
            const startTime = timeSlotsContainer.find('.time-btn.active').data('time') || '';
            const notes = $('#noteGroup textarea').val().trim();

            console.log("TIME" + startTime);


            let errors = [];
            if (!bookingDate) {
                errors.push('Vui lòng chọn ngày đặt lịch.');
            }

            if (!serviceId) {
                errors.push('Vui lòng chọn dịch vụ.');
            }
            if (!startTime) {
                errors.push('Vui lòng chọn khung giờ.');
            }

            if (errors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Đặt lịch thất bại',
                    text: errors.join('\n')
                })
                return;
            }

            $.ajax({
                url: '/api/appointments/book',
                method: 'POST',
                contentType: 'application/json',
                headers: {
                    'Accept': 'application/json'
                },
                data: JSON.stringify({
                    customer_id: customerId,
                    appointment_date: bookingDate,
                    service_id: serviceId,
                    start_time: startTime,
                    notes: notes
                }),
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Đặt lịch thành công'
                        });
                        invalidateCache(bookingDate);
                        $('#bookingForm')[0].reset();
                        $('#noteGroup').hide();
                        timeSlotsContainer.html('');
                    }
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON?.errors || { message: 'Lỗi hệ thống.' };
                    Swal.fire({
                        icon: 'error',
                        title: 'Đặt lịch thất bại',
                        text: JSON.stringify(errors)
                    })
                }
            });

        });


        // validate time slot base on service duration

        const timeToMinutes = (timeStr) => {
            const [hours, minutes] = timeStr.split(':').map(Number);
            return hours * 60 + minutes;
        };

        const minutesToTime = (minutes) => {
            const hours = Math.floor(minutes / 60);
            const mins = minutes % 60;
            return `${hours.toString().padStart(2, '0')}:${mins.toString.padStart(2, '0')}`;
        };

        const isTimeSlotValid = (startTime, serviceDuration) => {
            if (!serviceDuration) {
                return true;
            }

            const startMinutes = timeToMinutes(startTime);
            const closingMinutes = timeToMinutes(STORE_CLOSING_TIME);
            const endMinutes = startMinutes + parseInt(serviceDuration);

            return endMinutes <= closingMinutes;
        };

        const updateTimeSlotsBasedOnDuration = (serviceDuration) => {
            timeSlotsContainer.find('.time-btn').each(function () {
                const $btn = $(this);
                const timeSlot = $btn.data('time');

                if (!isTimeSlotValid(timeSlot, serviceDuration)) {
                    $btn.prop('disabled', true)
                        .removeClass('active')
                        .addClass('insufficient-time')
                        .attr('title', `Không đủ thời gian (cần ${serviceDuration}p, cửa hàng đóng ${STORE_CLOSING_TIME})`);
                }
            });
        };

    const resetTimeSlots = () => {
        timeSlotsContainer.find('.time-btn').each(function () {
            const $btn = $(this);

            if ($btn.hasClass('insufficient-time')) {
                $btn.prop('disabled', false)
                    .removeClass('insufficient-time')
                    .removeAttr('title');
            }
        });
    };



    });
</script>