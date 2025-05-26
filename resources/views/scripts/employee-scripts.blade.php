<script>
    $(document).ready(function (){
        $.ajax({
            url: 'api/employees/getAll',
            method: 'GET',
            success: function(res){
                if (res.success && res.data.length > 0){
                    let html = '';
                    res.data.forEach(mechanic => {
                    const stars = generateStars(mechanic.rating);
                        html += `
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm h-100">
                                <img src="https://res.cloudinary.com/dhis8yzem/image/upload/v1747907535/mechanic_jx5m54.png" class="card-img-top" alt="Hình ảnh thợ">
                                <div class="card-body">
                                    <h5 class="card-title">${mechanic.name}</h5>
                                    <p class="card-text mb-1">Kinh nghiệm: ${mechanic.experience} năm</p>
                                    <div class="text-warning">${stars}</div>
                                    <p>${mechanic.is_active == 1 ? '<span class="text-success">Đang làm việc</span>' : '<span class="text-danger">Tạm nghỉ</span>'}</p>
                                </div>
                            </div>
                        </div>
                       `;
                    });
                        $('#mechanic-list').html(html);
                }else{
                     $('#mechanic-list').html('<div class="col-12"><p class="text-danger">Không có nhân viên nào.</p></div>');

                }
            }
        });


        function generateStars(rating) {
            let stars = '';
            const fullStars = Math.floor(rating);
            const hasHalf = rating - fullStars >= 0.5;
            for (let i = 1; i <= 5; i++) {
                if (i <= fullStars) {
                    stars += '<i class="bi bi-star-fill"></i>';
                } else if (i === fullStars + 1 && hasHalf) {
                    stars += '<i class="bi bi-star-half"></i>';
                } else {
                    stars += '<i class="bi bi-star"></i>';
                }
            }
            return stars;
        }

    });

</script>