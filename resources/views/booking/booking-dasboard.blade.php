@extends('layouts.admin')
@section('head')
    <link rel="stylesheet" href="{{ asset('css/booking-dashboard.css') }}">
@endsection
@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h5>Tìm kiếm Lịch hẹn</h5>
            </div>
            <div class="card-body">
                <form id="searchForm" method="get">
                    <div class="row">
                        <!-- Tên khách hàng -->
                        <div class="col-md-3 mb-3">
                            <label for="customer_name" class="form-label">Tên khách hàng</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name"
                                placeholder="Nhập tên khách hàng...">
                        </div>

                        <!-- Tên nhân viên -->
                        <div class="col-md-3 mb-3">
                            <label for="employee_name" class="form-label">Tên nhân viên</label>
                            <input type="text" class="form-control" id="employee_name" name="employee_name"
                                placeholder="Nhập tên nhân viên...">
                        </div>

                        <!-- Tên dịch vụ -->
                        <div class="col-md-3 mb-3">
                            <label for="service_name" class="form-label">Tên dịch vụ</label>
                            <input type="text" class="form-control" id="service_name" name="service_name"
                                placeholder="Nhập tên dịch vụ...">
                        </div>

                        <!-- Trạng thái -->
                        <div class="col-md-3 mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending">Chờ xác nhận
                                </option>
                                <option value="confirmed">Đã xác nhận
                                </option>
                                <option value="completed">Hoàn thành
                                </option>
                                <option value="cancelled">Đã hủy
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Từ ngày -->
                        <div class="col-md-3 mb-3">
                            <label for="date_from" class="form-label">Từ ngày</label>
                            <input type="date" class="form-control" id="date_from" name="date_from">
                        </div>

                        <!-- Đến ngày -->
                        <div class="col-md-3 mb-3">
                            <label for="date_to" class="form-label">Đến ngày</label>
                            <input type="date" class="form-control" id="date_to" name="date_to">
                        </div>

                        <!-- Giá từ -->
                        <div class="col-md-3 mb-3">
                            <label for="price_from" class="form-label">Giá từ</label>
                            <input type="number" class="form-control" id="price_from" name="price_from" placeholder="0">
                        </div>

                        <!-- Giá đến -->
                        <div class="col-md-3 mb-3">
                            <label for="price_to" class="form-label">Giá đến</label>
                            <input type="number" class="form-control" id="price_to" name="price_to" placeholder="1000000">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex align-items-center" style="gap: 1.5rem">
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-search"></i> Tìm kiếm
                                    </button>

                                    <a href="#" id="clearSearchBtn" class="btn btn-secondary me-4">
                                        <i class="fas fa-times"></i> Xóa bộ lọc
                                    </a>
                                </div>

                                <!-- Quick filters -->
                                <div class="btn-group align-self-center" role="group">
                                    <input type="radio" class="btn-radio" name="quick_filter" id="today" value="today"
                                        autocomplete="off">
                                    <label class="btn btn-outline-info mb-0" for="today">Hôm nay</label>

                                    <input type="radio" class="btn-radio" name="quick_filter" id="week" value="week"
                                        autocomplete="off">
                                    <label class="btn btn-outline-info mb-0" for="week">Tuần này</label>

                                    <input type="radio" class="btn-radio" name="quick_filter" id="month" value="month"
                                        autocomplete="off">
                                    <label class="btn btn-outline-info mb-0" for="month">Tháng này</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <!-- Kết quả tìm kiếm -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Khách hàng</th>
                                <th>Nhân viên</th>
                                <th>Dịch vụ</th>
                                <th>Ngày hẹn</th>
                                <th>Giờ bắt đầu</th>
                                <th>Giờ kết thúc</th>
                                <th>Số tiền</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="bookingTableBody">
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    Dữ liệu sẽ được hiển thị ở đây...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3" id="paginationWrapper">

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="partModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm phụ tùng sửa chữa xe</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="booking_id" id="modalBookingId">
                    <input type="hidden" name="customer_id" id="modalCustomerId">
                    <!-- 1. Thông tin xe (readonly) -->
                    <div class="mb-3">
                        <h6>Thông tin xe</h6>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Biển số</label>
                                <input type="text" class="form-control" id="license_plate" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Hãng xe</label>
                                <input type="text" class="form-control" id="brand" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Dòng xe</label>
                                <input type="text" class="form-control" id="model" readonly>
                            </div>
                            
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Năm sản xuất</label>
                                <input type="text" class="form-control" id="year_manufactory" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Số máy</label>
                                <input type="text" class="form-control" id="engine_number" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Số khung</label>
                                <input type="text" class="form-control" id="chassis_number" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Loại nhiên liệu</label>
                                <input type="text" class="form-control" id="fuel_type" readonly>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- 2. Form thêm phụ tùng -->
                    <div class="form-group">
                        <label>Danh sách phụ tùng</label>
                        <table class="table table-bordered" id="partsTable">
                            <thead>
                                <tr>
                                    <th>Phụ tùng</th>
                                    <th>Số lượng</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="partsTableBody">
                                <!-- Hàng mẫu sẽ được thêm động -->
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-success" id="addPartRow">
                            + Thêm phụ tùng
                        </button>
                    </div>

                    <div class="form-group">
                        <label>Chuẩn đoán lỗi</label>
                        <textarea class="form-control" id="diagnosis" rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Mô tả công việc sửa chữa</label>
                        <textarea class="form-control" id="work_description" rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Công việc đã thực hiện</label>
                        <textarea class="form-control" id="completed_work" rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Ghi chú kỹ thuật viên</label>
                        <textarea class="form-control" id="technician_note" rows="2"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="saveRepairData">Lưu thông tin sửa chữa</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    @include('scripts.booking-dashboard-scripts')
@endsection