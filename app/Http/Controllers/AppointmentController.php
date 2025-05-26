<?php

namespace App\Http\Controllers;

use AppointmentBookingService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    protected $bookingService;

    public function __construct(AppointmentBookingService $bookingService){
        $this->bookingService = $bookingService;
    }

    public function getAvailableTimeSlots(Request $request){
        try {
            $date = $request->input('date');
            $availableSlots = $this->bookingService->getAvailableTimeSlots($date);

            return response()->json([
                'success' => true,
                'data' => $availableSlots,
                'message' => 'Lấy danh sách slot thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bookAppointment()
}
