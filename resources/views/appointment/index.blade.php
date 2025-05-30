@extends('layouts.app')
@section('head')
    <link rel="stylesheet" href="{{ asset('css/appointment.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@endsection

@section('content')
    <div class="calendar-container">
        <div class="container-fluid">
            <!-- Header -->
            <div class="calendar-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-alt text-warning mr-3" style="font-size: 24px;"></i>
                            <div>
                                <h5 class="mb-0">Today, <span id="currentDate"></span></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="btn-group view-toggle" role="group">
                            <button type="button" class="btn" data-view="day">Day</button>
                            <button type="button" class="btn active" data-view="week">Week</button>
                            <button type="button" class="btn" data-view="month">Month</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="calendar-grid">
                <div class="row no-gutters">
                    <div class="col-1">
                        <div class="time-axis">
                            <div class="day-header" style="border-bottom: 1px solid #e9ecef;">
                                &nbsp;
                            </div>
                            <div class="time-slots" id="timeAxis">
                                <!-- Time labels will be generated here -->
                            </div>
                        </div>
                    </div>
                    <div class="col-11">
                        <div class="calendar-days" id="calendarDays">
                            <!-- Loading indicator -->
                            <div class="loading" id="loadingIndicator">
                                <i class="fas fa-spinner fa-spin"></i>
                                <p>Loading bookings...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Detail Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="bookingDetails">
                    <!-- Booking details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="cancelBooking">Cancel Booking</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
@endsection
@section('scripts')
    @include('scripts.appointment-scripts')
@endsection