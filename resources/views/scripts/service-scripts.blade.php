<script>
    $(document).ready(function () {
        $.ajax({
            url: 'api/services/getAll',
            method: 'GET',
            success: function (res) {
                if (res.success && res.data.length > 0) {
                    let html = '';
                    res.data.forEach(service => {
                        html += `
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">${service.service_name}</h5>
                                    <p class="card-text">${service.description}</p>
                                    <p class="card-text"><strong>Giá:</strong> ${parseFloat(service.base_price).toLocaleString()}đ</p>
                                    <p class="card-text"><strong>Thời gian:</strong> ${service.duration} phút</p>
                                </div>
                            </div>
                        </div>`;
                    });
                    $('#service-list').html(html);
                } else {
                    $('#service-list').html('<div class="col-12"><p class="text-danger">Không có dịch vụ nào.</p></div>');
                }
            },
            error: function () {
                $('#service-list').html('<div class="col-12"><p class="text-danger">Lỗi tải dữ liệu dịch vụ.</p></div>');
            }
        });
    });
</script>