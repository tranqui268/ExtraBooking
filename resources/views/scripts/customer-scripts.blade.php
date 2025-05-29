<script>
    $(document).ready(function(){

        fetchCustomers(1);
        function fetchCustomers(page = 1){
            const name = $('#searchName').val();
            const email = $('#searchEmail').val();
            const status = $('#searchStatus').val();
            const address = $('#searchAddress').val();
            const perPage = $('#pageSize').val();

            $.ajax({
                url: '{{ url("api/customers/") }}',
                type: 'GET',
                data: {name, email, status, address, page, perPage},
                success: function (res) {
                    let tableBody = $('#customerTableBody');
                    tableBody.empty();
                    if (res.data && res.data.length > 0) {
                        const startIndex = (page - 1) * res.pagination.page_size;
                        $.each(res.data, function (index, customer) {
                            let rowClass = customer.is_delete == 0 ? 'locked-row' : '';
                           
                            let row = `<tr class="${rowClass}">` +
                                '<td>' + customer.name + '</td>' +
                                '<td>' + customer.email + '</td>' +                               
                                '<td>' + (customer.phone) + '</td>' +
                                '<td>' + (customer.address) + '</td>' +
                                '<td>' + ("2") + '</td>' +
                                '<td>' + ("100000") + '</td>' +
                                '<td>' +
                                '<a class="editUser text-info mr-2" data-id="' + customer.id + '"><i class="bi bi-pencil-fill" style="color: blue"></i></a>' +
                                '</td>' +
                                '</tr>';
                            tableBody.append(row);
                        });
                        
                    } else {
                        tableBody.append('<tr><td colspan="5" class="text-center">Không có dữ liệu</td></tr>');
                    }
                    renderPagination(res);
                },
                error: function (xhr, status, error) {
                    $('#customerTableBody').html('<tr><td colspan="5" class="text-center text-danger">Lỗi khi tải dữ liệu</td></tr>');
                }
            });
        }

        function renderPagination(res) {
            let pagination = $('#pagination')
            pagination.empty();
            let paginationInfo = res.pagination;
            let currentPage = paginationInfo.current_page;
            let totalPages = paginationInfo.last_page;
            if (totalPages > 1) {
                if (currentPage > 1) {
                    pagination.append('<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage - 1) + '">Previous</a></li>');
                } else {
                    pagination.append('<li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>');
                }

                // Các số trang
                for (let i = 1; i <= totalPages; i++) {
                    if (i === currentPage) {
                        pagination.append('<li class="page-item active"><a class="page-link" href="#">' + i + '</a></li>');
                    } else {
                        pagination.append('<li class="page-item"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>');
                    }
                }

                // Nút Next
                if (currentPage < totalPages) {
                    pagination.append('<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage + 1) + '">Next</a></li>');
                } else {
                    pagination.append('<li class="page-item disabled"><a class="page-link" href="#">Next</a></li>');
                }

                // Gắn sự kiện cho các nút phân trang
                $('.page-link').click(function (e) {
                    e.preventDefault();
                    let page = $(this).data('page');
                    console.log(page);

                    if (page) {
                        fetchCustomers(page);
                    }
                });
            }
        }

        const debouncedFetchCustomers = _.debounce((page) => {
            fetchCustomers(page);
        }, 500);


        $('#searchForm').on('submit', function (e) {
            e.preventDefault();
            debouncedFetchCustomers(1);
        });

        $('#clearSearchBtn').on('click', function (e) {
            e.preventDefault();
            $('#searchForm')[0].reset();
            debouncedFetchCustomers(1);
        });

        $(document).on('click', '.editUser', function (e) {
            e.preventDefault();

            // Kiểm tra nếu có dòng đang chỉnh sửa
            if ($('tr.editing').length) {
                Swal.fire('Thông báo', 'Vui lòng lưu hoặc hủy dòng đang chỉnh sửa trước khi chỉnh sửa dòng khác.', 'info');
                return;
            }

            const row = $(this).closest('tr');
            const id = $(this).data('id');
            console.log(id);


            const name = row.find('td:eq(0)').text().trim();
            const email = row.find('td:eq(1)').text().trim();
            const address = row.find('td:eq(2)').text().trim();
            const tel = row.find('td:eq(3)').text().trim();

            
            row.find('td:eq(0)').html('<input type="text" class="form-control form-control-sm edit-name" value="' + name + '">');
            row.find('td:eq(1)').html('<input type="email" class="form-control form-control-sm edit-email" value="' + email + '">');
            row.find('td:eq(2)').html('<input type="text" class="form-control form-control-sm edit-address" value="' + address + '">');
            row.find('td:eq(3)').html('<input type="text" class="form-control form-control-sm edit-tel" value="' + tel + '">');

            // Thay nút "Edit" bằng "Save" và "Cancel"
            row.find('td:eq(6)').html('<a href="#" class="saveEdit text-success mr-2" data-id="' + id + '"><i class="bi bi-check-lg"></i></a>' +
                '<a href="#" class="cancelEdit text-secondary" data-id="' + id + '"><i class="bi bi-x-lg"></i></a>');

            row.addClass('editing');
        });

    });
</script>