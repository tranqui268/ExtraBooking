@extends('layouts.app')
@section('head')
   <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
@endsection
@section('content')
<div class="container py-4">
    <h3 class="mb-4 text-center">Đặt lịch sửa xe</h3>
    <form id="bookingForm">       
        <div class="form-group">
            <label for="bookingDate">Chọn ngày đặt lịch <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="bookingDate" required min="{{ date('Y-m-d') }}">
        </div>

        <div class="form-group">
            <label for="serviceSelect">Chọn dịch vụ <span class="text-danger">*</span></label>
            <select id="serviceSelect" class="form-control" required>
                <option value="">-- Chọn dịch vụ --</option>
            </select>
            <small id="durationText" class="form-text text-muted mt-1" style="display: none;"></small>
        </div>

        <div class="form-group form-check form-switch">
            <input type="checkbox" class="form-check-input" id="noteToggle">
            <label class="form-check-label" for="noteToggle">Ghi chú thêm</label>
        </div>

        <div class="form-group" id="noteGroup" style="display: none;">
            <textarea class="form-control" rows="3" placeholder="Nhập ghi chú..."></textarea>
        </div>

        <div class="form-group">
            <label>Chọn khung giờ dịch vụ <span class="text-danger">*</span></label>
            <div class="row" id="timeSlots">
                <!-- Các khung giờ sẽ được render ở đây -->
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block">
            <i class="bi bi-calendar-check"></i> Đặt lịch
        </button>
    </form>
</div>
@endsection

@section('scripts')
    @include('scripts.booking-scripts')
@endsection
