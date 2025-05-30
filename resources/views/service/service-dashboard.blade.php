@extends('layouts.admin')
@section('head') 
    <link rel="stylesheet" href="{{ asset('css/service-dashboard.css') }}"> 
    <script src="https://code.highcharts.com/highcharts.js"></script>
@endsection
@section('content')
<div class="p-4">
    <div class="row">
        <!-- Biểu đồ bên trái -->
        <div class="col-md-5">
            <div class="card shadow rounded-3">
                <div class="card-body">
                    <h5 class="card-title">Thống kê dịch vụ</h5>
                     {!! $chart->container() !!}
                </div>
            </div>
        </div>

        <!-- Bảng quản lý dịch vụ bên phải -->
        <div class="col-md-7">
            <div class="card shadow rounded-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <input type="text" id="searchService" class="form-control w-50" placeholder="Tìm dịch vụ...">
                        <button class="btn btn-primary" id="addServiceBtn">+ Thêm dịch vụ</button>
                    </div>

                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Tên dịch vụ</th>
                                <th>Giá</th>
                                <th>Thời gian</th>
                                <th class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="serviceTableBody">
                            <!-- JS sẽ render -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm / Sửa dịch vụ -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="serviceForm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm dịch vụ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="serviceId">
                <div class="mb-3">
                    <label for="serviceName" class="form-label">Tên dịch vụ</label>
                    <input type="text" class="form-control" id="serviceName" required>
                    <small class="text-danger" id="nameError"></small>
                </div>
                <div class="mb-3">
                    <label for="servicePrice" class="form-label">Giá</label>
                    <input type="number" class="form-control" id="servicePrice" required>
                    <small class="text-danger" id="priceError"></small>
                </div>
                <div class="mb-3">
                    <label for="serviceDuration" class="form-label">Thời gian</label>
                    <input type="number" class="form-control" id="serviceDuration" required>
                    <small class="text-danger" id="durationError"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button id="saveBtn" type="button" class="btn btn-primary">Lưu</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
            </div>
        </div>
    </form>
  </div>
</div>
{!! $chart->script() !!}
@endsection
@section('scripts')
   @include('scripts.service-dashboard-scripts')
@endsection

