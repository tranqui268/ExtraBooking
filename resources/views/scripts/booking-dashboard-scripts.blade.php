<script>
    let partOptions = '';
    $(document).ready(function () {
        fetchAppointment(1);
        fetchPart();

        function fetchAppointment(page = 1) {
            const customerName = $('#customer_name').val();
            const employeeName = $('#employee_name').val();
            const serviceName = $('#service_name').val();
            const status = $('#status').val();
            const dateFrom = $('#date_from').val();
            const dateTo = $('#date_to').val();
            const priceFrom = $('#price_from').val();
            const priceTo = $('#price_to').val();

            $.ajax({
                url: '{{ url("api/appointments/") }}',
                method: 'GET',
                data: {
                    customer_name: customerName,
                    service_name: serviceName,
                    employee_name: employeeName,
                    status: status,
                    date_from: dateFrom,
                    date_to: dateTo,
                    price_from: priceFrom,
                    price_to: priceTo
                },
                success: function (response) {
                    let tableBody = $('#bookingTableBody');
                    tableBody.empty();
                    if (response.data && response.data.length > 0) {
                        $.each(response.data, function (index, booking) {
                            let row = '<tr>' +
                                '<td>' + (index + 1) + '</td>' +
                                '<td>' + (booking.customer.name) + '</td>' +
                                '<td>' + (booking.employee.name) + '</td>' +
                                '<td>' + (booking.service.service_name) + '</td>' +
                                '<td>' + (booking.appointment_date) + '</td>' +
                                '<td>' + (booking.start_time) + '</td>' +
                                '<td>' + (booking.end_time) + '</td>' +
                                '<td>' + (booking.total_amount) + '</td>' +
                                '<td>' + (booking.status) + '</td>' +
                                '<td>' +
                                '<a class="addPart text-info mr-2" data-id="' + booking.id + '" data-customer="' + booking.customer.id + '"><i class="bi bi-car-front"></i></a>'
                            '</td>' +
                                '</tr>';

                            tableBody.append(row);
                        });


                    } else {
                        tableBody.append('<tr><td colspan="9" class="text-center text-muted>Không có dữ liệu</td></tr>');
                    }
                },
                error: function (xhr, status, error) {
                    $('#bookingTableBody').html('<tr><td colspan="9" class="text-center text-muted>Lỗi khi tải dữ liệu</td></tr>')
                }
            })

        }

        const debouncedFetchAppointments = _.debounce((page) => {
            fetchAppointment(page);
        }, 500);

        $('#searchForm').on('submit', function (e) {
            e.preventDefault();
            debouncedFetchAppointments(1);
        });

        $('#clearSearchBtn').on('click', function (e) {
            e.preventDefault();
            $('#searchForm')[0].reset();
            debouncedFetchAppointments(1);
        });

        $(document).on('click', '.addPart', function (e) {
            let appointmentId = $(this).data('id');
            let customerId = $(this).data('customer');

            $('#partModal').modal('show');

            $('#modalBookingId').val(appointmentId);
            $('#modalCustomerId').val(customerId);

            fetchVehicle(customerId);
        });

        function fetchVehicle(customerId) {
            const baseUrl = '{{ url('api/vehicles/byCustomer') }}';
            const urlStr = `${baseUrl}/${customerId}`;
            $.ajax({
                url: urlStr,
                method: 'GET',
                success: function (res) {
                    if (res.success && res.data) {
                        const vehicle = res.data;
                        $('#license_plate').val(vehicle.license_plate);
                        $('#brand').val(vehicle.brand);
                        $('#model').val(vehicle.model);
                        $('#year_manufactory').val(vehicle.year_manufactory);
                        $('#engine_number').val(vehicle.engine_number);
                        $('#chassis_number').val(vehicle.chassis_number);
                        $('#fuel_type').val(vehicle.fuel_type);
                    }
                }
            });
        }

        function fetchPart() {
            $.ajax({
                url: '{{ url("api/parts/getAll") }}',
                method: 'GET',
                success: function (res) {
                    if (res.data && res.data.length > 0) {
                        partOptions = `<option value="">-- Chọn phụ tùng --</option>`;
                        $.each(res.data, function (index, part) {
                            partOptions += `  
                                <option value="${part.id}">${part.name}</option>
                            `;
                        });
                    }
                }
            });
        }

        $('#addPartRow').click(function () {
            const row = `
                <tr>
                    <td>
                        <select class="form-control part-select" name="parts[]">
                            ${partOptions}
                        </select>
                    </td>
                    <td>
                        <input type="number" name="quantities[]" class="form-control" min="1" value="1">
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-part">X</button>
                    </td>
                </tr>
            `;
            $('#partsTableBody').append(row);

        });

        $(document).on('click', '.remove-part', function () {
            $(this).closest('tr').remove();
        });

        $('#saveRepairData').click(function () {
            if (!validateRepairForm()) {
                return;
            }
            let partId = [];
            let quantity = [];
            $('#partsTableBody tr').each(function () {
                const selectedPart = $(this).find('select[name="parts[]"]').val();
                const selectedQty = $(this).find('input[name="quantities[]"]').val();

                partId.push(selectedPart);
                quantity.push(selectedQty);
            });

            const diagnosisText = $('#diagnosis').val();
            const workDescriptionText = $('#work_description').val();
            const completedWorkText = $('#completed_work').val();
            const technicianNoteText = $('#technician_note').val();
            const appointmentId = $('#modalBookingId').val();

            $.ajax({
                url: '{{ url("api/repairOrders/create") }}',
                method: 'POST',
                data: {
                    parts: partId,
                    quantities: quantity,
                    appointmentId: appointmentId,
                    description: workDescriptionText,
                    diagnosis: diagnosisText,
                    workPerformed: workDescriptionText,
                    technicianNotes: technicianNoteText,
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thêm thành công',
                    });
                    $('#partModal').modal('hide');
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON?.message || { message: 'Lỗi hệ thống.' };
                    Swal.fire({
                        icon: 'error',
                        title: 'Thêm thất bại',
                        text: JSON.stringify(errors)
                    });
                }

            });
        });

        function isValidText(input) {
            const trimmed = input.trim();
            const dangerousChars = /['";]/;

            if (trimmed.length === 0 || dangerousChars.test(trimmed) || trimmed.includes('--')) {
                return false;
            }

            return true;
        }


        function validateRepairForm() {
            let isValid = true;
            let errorMessages = [];


            let partValid = true;
            $('#partsTableBody tr').each(function (index) {
                const selectedPart = $(this).find('select[name="parts[]"]').val();
                const selectedQty = $(this).find('input[name="quantities[]"]').val();

                if (!selectedPart || selectedQty <= 0) {
                    partValid = false;
                    errorMessages.push(`Phụ tùng hàng ${index + 1} chưa hợp lệ.`);
                }
            });

            if (!partValid) isValid = false;

            const fields = [
                { value: $('#diagnosis').val(), label: 'Chuẩn đoán lỗi' },
                { value: $('#work_description').val(), label: 'Mô tả công việc' },
                { value: $('#completed_work').val(), label: 'Công việc đã thực hiện' },
                { value: $('#technician_note').val(), label: 'Ghi chú kỹ thuật viên' },
            ];

            fields.forEach(field => {
                if (!isValidText(field.value)) {
                    isValid = false;
                    errorMessages.push(`${field.label} không được để trống hoặc chứa ký tự nguy hiểm.`);
                }
            });

            if (!isValid) {
                alert("Lỗi nhập liệu:\n" + errorMessages.join("\n"));
            }

            return isValid;
        }




    });
</script>