<script>
    $(document).ready(function () {
        fetchService();

        function fetchService() {
            const serviceName = $('#searchService').val();

            $.ajax({
                url: '{{ url("api/services/") }}',
                type: 'GET',
                data: {
                    service_name: serviceName
                },
                success: function (res) {
                    let tableBody = $('#serviceTableBody');
                    tableBody.empty();
                    if (res.data && res.data.length > 0) {
                        $.each(res.data, function (index, service) {
                            let row = '<tr>' +
                                '<td>' + service.service_name + '</td>' +
                                '<td>' + parseFloat(service.base_price).toLocaleString() + '</td>' +
                                '<td>' + service.duration + '</td>' +
                                `<td class="text-end">
                                    <button id="btnEdit" class="btn btn-sm btn-warning me-1" data-id="${service.id}">Sửa</button>
                                    <button id="btnDelete" class="btn btn-sm btn-danger" data-id="${service.id}">Xóa</button>
                                </td>`+
                                '</tr>';

                            tableBody.append(row);
                        });
                    } else {
                        tableBody.append('<tr><td colspan="5" class="text-center">Không có dữ liệu</td></tr>');
                    }
                },
                error: function (xhr, status, error) {
                    $('#serviceTableBody').html('<tr><td colspan="5" class="text-center text-danger">Lỗi khi tải dữ liệu</td></tr>');
                }

            });
        }

        function debounce(func, delay) {
            let timer;
            return function (...args) {
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(this, args), delay);
            };
        }

        $('#searchService').on('input', debounce(fetchService, 500));

        // Thêm dịch vụ
        $('#addServiceBtn').on('click', function () {
            $('#serviceForm')[0].reset();
            $('.modal-title').text('Thêm dịch vụ');
            $('#serviceId').val(0);
            $('#serviceModal').modal('show');
        });


        // Sửa dịch vụ
        $(document).on('click', '#btnEdit', function () {
            $('#serviceForm')[0].reset();
            const $icon = $(this);
            $('.modal-title').text('Sửa dịch vụ');

            const name = $icon.closest('tr').find('td').eq(0).text();
            $('#serviceName').val(name);

            const price = $icon.closest('tr').find('td').eq(1).text();
            const priceNumber = parseFloat(price.replace(/\./g, ''));
            $('#servicePrice').val(priceNumber);

            const duration = $icon.closest('tr').find('td').eq(2).text();
            $('#serviceDuration').val(duration);

            const id = $icon.data('id');
            $('#serviceId').val(id);

            $('#serviceModal').modal('show');
        });

        $('#serviceModal').on('show.bs.modal', function () {
            const errorIds = ['#nameError', '#priceError', '#durationError'];
            errorIds.forEach(id => $(id).text(''));
        });

        $('#saveBtn').on('click', function () {
            let name = $('#serviceName').val().trim();
            let price = $('#servicePrice').val().trim();
            let duration = $('#serviceDuration').val().trim();
            let serviceId = $('#serviceId').val();

            const errorIds = ['#nameError', '#priceError', '#durationError'];
            errorIds.forEach(id => $(id).text(''));

            if (validate(name, price, duration)) {
                return;
            }

            let method = serviceId != 0 ? 'PUT' : 'POST';
            let path = method === 'PUT' ? `api/services/${serviceId}` : 'api/services';

            $.ajax({
                url: `{{ url('/') }}/${path}`,
                method: method,
                data: {
                    id: serviceId,
                    service_name: name,
                    base_price: price,
                    duration: duration,
                },
                success: function () {
                    Swal.fire('Thành công', serviceId != 0 ? "Sửa thành công" : "Thêm thành công", 'success');
                    $('#serviceForm')[0].reset();
                    $('#serviceModal').modal('hide');
                    fetchService();
                },
                error: function() {
                    Swal.fire('Lỗi', serviceId != 0 ? "Lỗi khi sửa" : "Lỗi khi thêm", 'error');
                    $('#serviceModal').modal('hide');
                }
            });

        })





    });

    function validate(name, price, duration) {
        let specialCharRegex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;
        let numRegex = /^\d+$/;

        let hasError = false;

        if (!name) {
            $('#nameError').text('Tên dịch vụ không được để trống');
            hasError = true;
        } else if (specialCharRegex.test(name)) {
            $('#nameError').text('Tên không được chứa ký tự đặc biệt');
            hasError = true;
        }

        if (!price) {
            $('#priceError').text('Giá không được để trống');
            hasError = true;
        } else if (!numRegex.test(price) || parseInt(price) < 0) {
            $('#priceError').text('Giá không hợp lệ');
            hasError = true;
        }

        if (!duration) {
            $('#durationError').text('Thời gian không được để trống');
            hasError = true;
        } else if (!numRegex.test(duration) || parseInt(duration) < 0) {
            $('#durationError').text('Thời gian không hợp lệ');
            hasError = true;
        }
    }
</script>