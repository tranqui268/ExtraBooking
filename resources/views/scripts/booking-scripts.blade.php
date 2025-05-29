<script>
    $(document).ready(function () {
        const serviceSelect = document.getElementById('serviceSelect');
        const durationText = document.getElementById('durationText');
        const noteToggle = document.getElementById('noteToggle');
        const noteGroup = document.getElementById('noteGroup');
        const timeSlotsContainer = $('#timeSlots');

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
                    })
                }
            }
        });

        // Hiển thị thời gian thực hiện
        serviceSelect.addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];
            const duration = selected.dataset.duration;
            if (duration) {
                durationText.style.display = 'block';
                durationText.textContent = `Thời gian thực hiện: ${duration} phút.`;
            } else {
                durationText.style.display = 'none';
            }
        });

        const generateTimeSlots = () => {
            const selectedDate = document.getElementById('bookingDate').value;
            console.log(selectedDate);

            $.ajax({
                url: 'api/timeslots/generate-slot',
                method: 'POST',
                dataType: 'json',
                data: {
                    date: selectedDate
                },
                success: function (res) {
                    console.log(res.success);
                    
                    timeSlotsContainer.html('');
                    if (res.success && res.data && res.data.length > 0) {
                        res.data.forEach(slot => {
                            const col = $('<div>').addClass('col-3 mb-2');
                            const button = $('<button>')
                                .attr('type', 'button')
                                .addClass('btn btn-outline-secondary w-100 time-btn')
                                .attr('data-time', slot.time)
                                .prop('disabled', slot.disabled)
                                .text(slot.time);
                            col.append(button);
                            timeSlotsContainer.append(col);
                        });
                    } else {
                        timeSlotsContainer.html('<div class="text-center text-danger">Không có khung giờ khả dụng.</div>');
                    }
                }
            });

            timeSlotsContainer.on('click', '.time-btn:not(:disabled)', function () {
                timeSlotsContainer.find('.time-btn').removeClass('active');
                $(this).addClass('active');
                console.log('Selected time:', $(this).data('time')); // Debug
            });
        };

        document.getElementById('bookingDate').addEventListener('change', generateTimeSlots);
        // generateTimeSlots(); 

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
                    Swal.fire({
                        icon: 'success',
                        title: 'Đặt lịch thành công'
                    })
                    $('#bookingForm')[0].reset();
                    $('#noteGroup').hide();
                    timeSlotsContainer.html('');
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
    });
</script>