<script>
    let currentRatingRepairId = null;
    let selectedRating = 0;

    // Format currency
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }

    // Format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    // Generate stars HTML
    function generateStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                stars += '<i class="fas fa-star rating-stars"></i>';
            } else {
                stars += '<i class="far fa-star rating-stars"></i>';
            }
        }
        return stars;
    }

    // Search form handler
    $('#searchForm').on('submit', function (e) {
        e.preventDefault();
        const searchValue = $('#searchInput').val().trim();

        if (!searchValue) return;

        // Show loading
        $('#loadingSection').show();
        $('#resultsSection').hide();
        $('#noResultsSection').hide();

        displayResults();

    });

    function displayVehicleInfo() {
        const searchValue = $('#searchInput').val().trim();
        $.ajax({
            url: '{{ url("api/vehicles/lookup") }}',
            method: 'GET',
            data: {
                input: searchValue
            },
            success: function (response) {
                const vehicle = response.data;
                $('#vehiclePlate').text(vehicle.license_plate);
                $('#vehicleType').text(vehicle.brand +' '+ vehicle.model);
                $('#ownerName').text(vehicle.customer_name);
                $('#ownerPhone').text(vehicle.customer_phone);
                $('#vehicleYear').text(vehicle.year_manufactory);
                $('#totalRepairs').text(vehicle.repair_count + ' lần');
                $('#resultsSection').show();
            },
            error: function (xhr, status, error) {
                console.error('Error fetching vehicle info:', error);
                $('#loadingSection').hide();
                $('#noResultsSection').show();
            }
        });

    }

    // Display results
    function displayResults() {
        const searchValue = $('#searchInput').val().trim();

        // Display vehicle info
        displayVehicleInfo();

        $.ajax({
            url: '{{ url("api/repairOrders/lookup") }}',
            method: 'GET',
            data: {
                input: searchValue
            },
            success: function (response) {
                $('#loadingSection').hide();
                if (response.data && response.data.length > 0) {
                    const repairs = response.data;

                    // Display repair history
                    const historyHTML = repairs.map(repair => {
                        const servicesHTML = `<span class="service-tag">${repair.appointment.service.service_name}</span>`;

                        const partsHTML = repair.repair_order_part.map(part =>
                            `<span class="part-tag">${part.part.name}</span>`
                        ).join('');

                        let ratingHTML = '';
                        if (repair.review && Array.isArray(repair.review) && repair.review.length > 0) {
                            const reviewsHTML = repair.review.map(r => `
                                    <div class="mb-2">
                                        <strong>Đánh giá:</strong> ${generateStars(r.rating)}
                                        <small class="text-muted">(${r.rating}/5)</small>
                                        ${r.created_at ? `<small class="text-muted ml-2">${formatDate(r.created_at)}</small>` : ''}
                                    </div>
                                    ${r.comment ? `
                                    <div class="comment-section mb-2">
                                        <small><strong>Nhận xét:</strong> ${r.comment}</small>
                                    </div>` : ''}
                                `).join('');

                            ratingHTML = `
                                    <div class="reviews-section">
                                        ${reviewsHTML}
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm rate-btn mt-2" data-repair-id="${repair.id}" data-repair-date="${repair.appointment.appointment_date}">
                                        <i class="fas fa-plus"></i> Thêm đánh giá
                                    </button>
                                `;
                        } else {
                            ratingHTML = `<button class="btn btn-outline-warning btn-sm rate-btn" data-repair-id="${repair.id}" data-repair-date="${repair.appointment.appointment_date}">
                                    <i class="fas fa-star"></i> Đánh giá dịch vụ
                                </button>`;
                        }

                        return `
                        <div class="card history-card mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5 class="card-title">
                                            <i class="fas fa-calendar-alt text-primary"></i>
                                            ${formatDate(repair.appointment.appointment_date)}
                                        </h5>
                                        
                                        <div class="mb-3">
                                            <strong><i class="fas fa-tools text-success"></i> Dịch vụ thực hiện:</strong>
                                            <div class="mt-2">${servicesHTML}</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <strong><i class="fas fa-cogs text-info"></i> Phụ tùng thay thế:</strong>
                                            <div class="mt-2">${partsHTML}</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <strong><i class="fas fa-sticky-note text-warning"></i> Ghi chú kỹ thuật:</strong>
                                            <p class="text-muted mt-1 mb-0">${repair.technician_notes}</p>
                                        </div>
                                        
                                        ${ratingHTML}
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <div class="cost-highlight">
                                            <i class="fas fa-money-bill-wave"></i><br>
                                            ${formatCurrency(repair.total_cost)}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                `;
                    }).join('');

                    $('#repairHistory').html(historyHTML);
                    $('#resultsSection').show();
                }

            }
        })


    }

    // Rating button handler
    $(document).on('click', '.rate-btn', function () {
        currentRatingRepairId = $(this).data('repair-id');
        const repairDate = $(this).data('repair-date');

        $('#modalRepairDate').text(formatDate(repairDate));
        $('#ratingModal').modal('show');

        // Reset rating
        selectedRating = 0;
        $('.rating-input').removeClass('active');
        $('#ratingText').text('Chưa đánh giá');
        $('#commentText').val('');
    });

    // Rating stars handler
    $(document).on('click', '.rating-input', function () {
        selectedRating = $(this).data('rating');

        $('.rating-input').removeClass('active');
        for (let i = 1; i <= selectedRating; i++) {
            $(`.rating-input[data-rating="${i}"]`).addClass('active');
        }

        const ratingTexts = ['', 'Rất tệ', 'Tệ', 'Bình thường', 'Tốt', 'Rất tốt'];
        $('#ratingText').text(ratingTexts[selectedRating]);
    });

    // Submit rating
    $('#submitRating').on('click', function () {
        if (selectedRating === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: 'Vui lòng chọn số sao đánh giá'
            });
            return;
        }

        const comment = $('#commentText').val().trim();

       $.ajax({
            url: '{{ url("api/reviews/") }}',
            method: 'POST',
            data: {
                repair_order_id: currentRatingRepairId,
                rating: selectedRating,
                comment: comment,
                response: 'ok'
            },
            success: function(response){
                $('#ratingModal').modal('hide');

                 Swal.fire({
                    icon: 'success',
                    title: 'Đánh giá thành công',
                    text: 'Cảm ơn bạn đã đánh giá! Phản hồi của bạn rất quan trọng với chúng tôi.'
                });
                displayResults();
                
            },
            error: function(xhr, status, error) {
                console.error('Error submitting rating:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: error.responseJSON?.message || error
                });
            }
       })
    });

    // Rating stars hover effect
    $(document).on('mouseenter', '.rating-input', function () {
        const rating = $(this).data('rating');
        $('.rating-input').removeClass('active');
        for (let i = 1; i <= rating; i++) {
            $(`.rating-input[data-rating="${i}"]`).addClass('active');
        }
    });

    $(document).on('mouseleave', '#ratingStars', function () {
        $('.rating-input').removeClass('active');
        for (let i = 1; i <= selectedRating; i++) {
            $(`.rating-input[data-rating="${i}"]`).addClass('active');
        }
    });
</script>