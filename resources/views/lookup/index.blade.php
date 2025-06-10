@extends('layouts.app')
@section('head')
<link rel="stylesheet" href="{{ asset('css/lookup.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@endsection()
@section('content')
    <div class="container main-container">
        <!-- Header -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-8 text-center">
                <h1 class="text-black mb-3">
                    <i class="fas fa-car-mechanic"></i>
                    Tra cứu lịch sử sửa chữa xe
                </h1>
                <p class="text-black-50">Nhập số điện thoại hoặc biển số xe để xem lịch sử sửa chữa</p>
            </div>
        </div>

        <!-- Search Form -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-8">
                <div class="card search-card">
                    <div class="card-body p-4">
                        <form id="searchForm">
                            <div class="form-group">
                                <label for="searchInput" class="font-weight-bold">
                                    <i class="fas fa-search"></i> Thông tin tìm kiếm
                                </label>
                                <input type="text" class="form-control form-control-lg" id="searchInput"
                                    placeholder="Nhập số điện thoại hoặc biển số xe (VD: 0123456789 hoặc 30A-12345)"
                                    required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-search btn-block">
                                <i class="fas fa-search"></i> Tra cứu lịch sử
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading -->
        <div id="loadingSection" class="loading" style="display: none;">
            <div class="spinner-border" role="status">
                <span class="sr-only">Đang tải...</span>
            </div>
            <p class="mt-3 text-white">Đang tìm kiếm thông tin...</p>
        </div>

        <!-- Results Section -->
        <div id="resultsSection" style="display: none;">
            <!-- Vehicle Info -->
            <div class="row justify-content-center mb-4">
                <div class="col-md-10">
                    <div class="vehicle-info">
                        <div class="row">
                            <div class="col-md-6">
                                <h4><i class="fas fa-car"></i> Thông tin xe</h4>
                                <p class="mb-1"><strong>Biển số:</strong> <span id="vehiclePlate"></span></p>
                                <p class="mb-1"><strong>Loại xe:</strong> <span id="vehicleType"></span></p>
                                <p class="mb-0"><strong>Chủ xe:</strong> <span id="ownerName"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Số điện thoại:</strong> <span id="ownerPhone"></span></p>
                                <p class="mb-1"><strong>Năm sản xuất:</strong> <span id="vehicleYear"></span></p>
                                <p class="mb-0"><strong>Tổng lần sửa:</strong> <span id="totalRepairs"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Repair History -->
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <h3 class="text-white mb-4">
                        <i class="fas fa-history"></i> Lịch sử sửa chữa
                    </h3>
                    <div id="repairHistory"></div>
                </div>
            </div>
        </div>

        <!-- No Results -->
        <div id="noResultsSection" class="no-results" style="display: none;">
            <div class="card search-card">
                <div class="card-body">
                    <i class="fas fa-search text-muted" style="font-size: 3em;"></i>
                    <h4 class="mt-3">Không tìm thấy thông tin</h4>
                    <p class="text-muted">Vui lòng kiểm tra lại số điện thoại hoặc biển số xe</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating Modal -->
    <div class="modal fade" id="ratingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-star"></i> Đánh giá chất lượng dịch vụ
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <h6>Ngày sửa: <span id="modalRepairDate"></span></h6>
                        <p class="text-muted">Hãy đánh giá chất lượng dịch vụ của chúng tôi</p>
                    </div>

                    <div class="text-center mb-4">
                        <div id="ratingStars">
                            <i class="fas fa-star rating-input" data-rating="1"></i>
                            <i class="fas fa-star rating-input" data-rating="2"></i>
                            <i class="fas fa-star rating-input" data-rating="3"></i>
                            <i class="fas fa-star rating-input" data-rating="4"></i>
                            <i class="fas fa-star rating-input" data-rating="5"></i>
                        </div>
                        <p class="mt-2 mb-0" id="ratingText">Chưa đánh giá</p>
                    </div>

                    <div class="form-group">
                        <label for="commentText">Nhận xét của bạn:</label>
                        <textarea class="form-control" id="commentText" rows="4"
                            placeholder="Chia sẻ trải nghiệm của bạn về chất lượng dịch vụ..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="submitRating">
                        <i class="fas fa-paper-plane"></i> Gửi đánh giá
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @include('scripts.lookup-scripts');
@endsection