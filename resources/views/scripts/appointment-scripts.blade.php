<script>
    $(document).ready(function () {
        let currentWeek = new Date();
        let currentView = 'week';
        let slotsData = null;

        updateDateDisplay();
        generateTimeAxis();
        loadBookings();

        $('.view-toggle .btn').click(function () {
            $('.view-toggle .btn').removeClass('active');
            $(this).addClass('active');
            currentView = $(this).data('view');
            loadBookings();
        });

        function updateDateDisplay() {
            const options = { year: 'numeric', month: 'long' };
            const dateStr = currentWeek.toLocaleDateString('en-US', options);
            console.log(dateStr);

            $('#currentDate').text(dateStr);
        }

        function generateTimeAxis() {
            const timeAxis = $('#timeAxis');
            timeAxis.empty();

            $.ajax({
                url: 'api/timeslots/generate-slot',
                method: 'POST',
                dataType: 'json',
                data: {
                    date: formattedDate(currentWeek)
                },
                success: function (res) {
                    if (res.success && res.data && res.data.length > 0) {
                        slotsData = res.data;
                        res.data.forEach(slot => {
                            const timeSlot = $(`
                                <div class="time-slot">
                                    <div class="time-label">${slot.time}</div>
                                </div>
                            `);
                            timeAxis.append(timeSlot);
                        });
                    }
                }
            });
        }

        function loadBookings() {
            $('#loadingIndicator').show();
            $('#calendarDays').addClass('loading');

            const requestData = {
                view: currentView,
                date: formattedDate(currentWeek),
            };

            $.ajax({
                url: '{{ url("api/appointments/bookings") }}',
                method: 'GET',
                data: requestData,
                success: function (response) {
                    renderCalendar(response.data);
                    $('#loadingIndicator').hide();
                    $('#calendarDays').removeClass('loading');
                },
                error: function (xhr, status, error) {
                    console.error('Error loading bookings:', error);
                    $('#loadingIndicator').hide();
                    $('#calendarDays').html('<div class="no-bookings">Error loading bookings. Please try again.</div>');
                }
            });
        }

        function renderCalendar(bookings) {
            const calendarDays = $('#calendarDays');
            calendarDays.empty();

            if (currentView === 'week') {
                renderWeekView(bookings);
            }
        }

        function renderWeekView(bookings) {
            const calendarDays = $('#calendarDays');
            const weekDays = getWeekDays();

            weekDays.forEach((day, index) => {
                const dayColumn = $(`
                    <div class="day-column">
                        <div class="day-header">
                            <div class="day-name">${day.name}</div>
                            <div class="day-number">${day.number}</div>
                        </div>
                        <div class="time-slots" data-date="${day.fullDate}">
                            ${generateTimeSlots()}
                        </div>
                    </div>

                `);

                const dayBookings = bookings.filter(booking =>
                    booking.date === day.fullDate
                );

                dayBookings.forEach(booking => {
                    const bookingElement = createBookingElement(booking);
                    positionBooking(dayColumn.find('.time-slots'), bookingElement, booking);
                });

                calendarDays.append(dayColumn);

            });
        }

        function generateTimeSlots() {
            let slots = '';
            slotsData.forEach(slot => {
                slots += `<div class="time-slot" data-hour="${slot.time}"></div>`;
            })
            return slots;
        }

        function getWeekDays() {
            const days = [];
            const startOfWeek = new Date(currentWeek);
            startOfWeek.setDate(currentWeek.getDate() - currentWeek.getDay() + 1);

            for (let i = 0; i < 6; i++) {
                const day = new Date(startOfWeek);
                day.setDate(startOfWeek.getDate() + i);

                days.push({
                    name: day.toLocaleDateString('en-US', { weekday: 'short' }),
                    number: day.getDate(),
                    fullDate: day.toISOString().split('T')[0]
                });
            }
            return days;
        }

        function createBookingElement(booking) {
            const [startHour, startMinute] = booking.start_time.split(':').map(Number);
            const [endHour, endMinute] = booking.end_time.split(':').map(Number);
            const durationMinutes = (endHour * 60 + endMinute) - (startHour * 60 + startMinute);

            // Add class based on duration for styling
            const sizeClass = durationMinutes < 60 ? 'small' : '';

            const timeDisplay = `${booking.start_time} - ${booking.end_time}`;

            return $(`
                    <div class="booking-item ${sizeClass}" data-booking-id="${booking.id}" title="${booking.customer_name} - ${booking.service} (${timeDisplay})">
                        <div class="booking-avatar">${booking.customer_name.charAt(0)}</div>
                        <div class="flex-grow-1" style="min-width: 0;">
                            <div class="booking-title text-truncate">${booking.customer_name}</div>
                            <div class="booking-subtitle text-truncate">${booking.service}</div>
                        </div>
                    </div>
                `);
        }

        function calculateBookingPositions(bookings) {
            if (!bookings.length) return [];

            // Convert time to minutes for easier calculation
            const bookingsWithMinutes = bookings.map(booking => {
                const [startHour, startMinute] = booking.start_time.split(':').map(Number);
                const [endHour, endMinute] = booking.end_time.split(':').map(Number);

                return {
                    ...booking,
                    startMinutes: startHour * 60 + startMinute,
                    endMinutes: endHour * 60 + endMinute,
                    column: 0,
                    totalColumns: 1
                };
            });

            // Sort by start time, then by duration (shorter first)
            bookingsWithMinutes.sort((a, b) => {
                if (a.startMinutes !== b.startMinutes) {
                    return a.startMinutes - b.startMinutes;
                }
                return (a.endMinutes - a.startMinutes) - (b.endMinutes - b.startMinutes);
            });

            // Group overlapping bookings
            const groups = [];
            let currentGroup = [bookingsWithMinutes[0]];

            for (let i = 1; i < bookingsWithMinutes.length; i++) {
                const current = bookingsWithMinutes[i];
                const lastInGroup = currentGroup[currentGroup.length - 1];

                // Check if current booking overlaps with any booking in current group
                const overlaps = currentGroup.some(booking =>
                    current.startMinutes < booking.endMinutes &&
                    current.endMinutes > booking.startMinutes
                );

                if (overlaps) {
                    currentGroup.push(current);
                } else {
                    groups.push([...currentGroup]);
                    currentGroup = [current];
                }
            }
            groups.push(currentGroup);

            // Calculate positions for each group
            groups.forEach(group => {
                const totalColumns = group.length;
                group.forEach((booking, index) => {
                    booking.column = index;
                    booking.totalColumns = totalColumns;
                });
            });

            return bookingsWithMinutes;
        }

        function positionBooking(timeSlots, bookingElement, booking) {
            const [startHour, startMinute] = booking.start_time.split(':').map(Number);
            const [endHour, endMinute] = booking.end_time.split(':').map(Number);

            // Calculate duration in minutes
            const startMinutes = startHour * 60 + startMinute;
            const endMinutes = endHour * 60 + endMinute;
            const durationMinutes = endMinutes - startMinutes;

            // Calculate position from 8:00 AM
            const baseMinutes = 8 * 60; // 7:00 AM in minutes
            const offsetMinutes = startMinutes - baseMinutes;
            const slotHeight = 60; // Each slot is 60px

            // Position and size calculations
            const topPosition = (offsetMinutes / 60) * slotHeight;
            const height = Math.max((durationMinutes / 60) * slotHeight, 30); // Minimum 30px height

            // Calculate width and left position for overlapping bookings
            const columnWidth = 100 / booking.totalColumns;
            const leftPosition = (booking.column * columnWidth);
            const width = columnWidth - 1; // Leave 1% gap between columns

            // Apply positioning
            bookingElement.css({
                'position': 'absolute',
                'top': topPosition + 'px',
                'height': height + 'px',
                'left': leftPosition + '%',
                'width': width + '%',
                'z-index': 10 + booking.column
            });

            // Add visual indicator for overlapping bookings
            if (booking.totalColumns > 1) {
                bookingElement.addClass('overlapping');
                bookingElement.attr('title',
                    `${booking.client_name} - ${booking.service_name} (${booking.start_time} - ${booking.end_time}) [${booking.column + 1}/${booking.totalColumns}]`
                );
            }

            // Find the container for this time period
            const containerSlot = timeSlots.find('.time-slot').first();
            containerSlot.parent().css('position', 'relative');
            containerSlot.parent().append(bookingElement);
        }

        $(document).on('click', '.booking-item', function () {
            const bookingId = $(this).data('booking-id');
            loadBookingDetails(bookingId);
        });

        function loadBookingDetails(bookingId) {
            $.ajax({
                url: `api/appointments/${bookingId}`,
                method: 'GET',
                success: function (response) {
                    const booking = response.data;
                    let statusClass = '';
                    switch (booking.status) {
                        case 'pending':
                            statusClass = 'text-warning';
                            break;
                        case 'confirmed':
                            statusClass = 'text-primary';
                            break;
                        case 'cancelled':
                            statusClass = 'text-danger';
                            break;
                        case 'completed':
                            statusClass = 'text-success';
                            break;
                        default:
                            statusClass = 'text-secondary';
                    }

                    const detailsHtml = `
                            <div class="row">
                                <input type="hidden" id="booking-id" value="${booking.id}">
                                <div class="col-md-6">
                                    <strong>Khách hàng:</strong> ${booking.customer_name}
                                </div>
                                <div class="col-md-6">
                                    <strong>Dịch vụ:</strong> ${booking.service}
                                </div>
                                <div class="col-md-6">
                                    <strong>Ngày đặt lịch:</strong> ${booking.date}
                                </div>
                                <div class="col-md-6">
                                    <strong>Thời gian:</strong> ${booking.start_time} - ${booking.end_time}
                                </div>
                                <div class="col-md-6">
                                    <strong>Trạng thái:</strong> <span class="${statusClass}">${booking.status}</span>
                                </div>
                                <div class="col-12">
                                    <strong>Ghi chú:</strong> ${booking.notes || 'No notes'}
                                </div>
                            </div>
                        `;
                    $('#bookingDetails').html(detailsHtml);
                    $('#bookingModal').modal('show');
                },
                error: function () {
                    alert('Error loading booking details');
                }

            });
        }

        $('#cancelBooking').on('click', function () {
            const bookingId = $('#booking-id').val();
            console.log('ID cần xử lý:', bookingId);

            $.ajax({
                url: `api/appointments/${bookingId}/cancel`,
                method: 'PUT',
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            text: response.message
                        })
                    }
                    $('#bookingModal').modal('hide');
                    loadBookings();

                },
                error: function (xhr) {
                    const errors = xhr.responseJSON?.message || { message: 'Lỗi hệ thống.' };
                    Swal.fire({
                        icon: 'error',
                        title: 'Hủy lịch thất bại',
                        text: JSON.stringify(errors)
                    })
                }

            });

        });

        function formattedDate(today) {
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');

            const formattedDate = `${yyyy}-${mm}-${dd}`;
            console.log(formattedDate);
            return formattedDate;
        }


    });
</script>