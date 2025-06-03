<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookAppointmentRequest;
use App\Repositories\Appointment\AppointmentRepositoryInterface;
use App\Services\AppointmentBookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class AppointmentController extends Controller
{
    protected $bookingService;
    protected $appointmentRepo;

    public function __construct(AppointmentBookingService $bookingService, AppointmentRepositoryInterface $appointmentRepo){
        $this->bookingService = $bookingService;
        $this->appointmentRepo = $appointmentRepo;
    }

    public function showAppointmentUser(){
        return view('appointment.index');
    }

    public function getWithFilters(Request $request){
        try {
            $appointments = $this->appointmentRepo->filters($request);
            return response()->json([
                'success' => true,
                'data' => $appointments->items(),
                'pagination' => [
                    'total' => $appointments->total(),
                    'page_size' => $appointments->perPage(),
                    'current_page' => $appointments->currentPage(),
                    'last_page' => $appointments->lastPage(),
                ],
                'message' => 'Lấy danh sách thành công'
            ]); 
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }

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

    public function bookAppointment(BookAppointmentRequest $request){
        try {
            $appointment = $this->bookingService->bookAppointment($request->validated());
            return response()->json([
                'success' => true,
                'data' => $appointment,
                'message' => 'Đặt lịch thành công'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đặt lịch thất bại: ' . $e->getMessage()
            ], 400);
        }
    }

    public function cancelAppointment($appointmentId)
    {
        try {
            $result = $this->bookingService->cancelAppointment($appointmentId);

            return response()->json([
                'success' => true,
                'message' => 'Hủy lịch hẹn thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hủy lịch hẹn thất bại: ' . $e->getMessage()
            ], 400);
        }
    }

    public function getAppointmentsByCustomer(int $customerId){
        try {
            $appointments = $this->appointmentRepo->getAppointmentsByCustomer($customerId);
            
            return response()->json([
                'success' => true,
                'message' => 'Lấy dữ liệu thành công',
                'data' => $appointments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAppointmentsUser(Request $request){
        try {
            $token = $request->bearerToken();
            $userFromToken = PersonalAccessToken::findToken($token)?->tokenable;

            Auth::setUser($userFromToken);

            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not authenticated.',
                    'token' => $request->bearerToken(), // debug xem token có không
                ], 401);
            }
            $view = $request->get('view', 'week');
            $date = $request->get('date', now()->format('Y-m-d'));

            $bookings = $this->appointmentRepo->getAppointmentsUser($user,$view,$date);
            Log::info($bookings);
            
            return response()->json([
                'success' => true,
                'data' => $bookings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getById($appointmentId){
        try {
            $appointment = $this->appointmentRepo->getById($appointmentId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $appointment->id,
                    'customer_name' => $appointment->customer->name,
                    'service' => $appointment->service->service_name,
                    'date' => $appointment->appointment_date,
                    'start_time' => $appointment->start_time->format('H:i:s'),
                    'end_time' => $appointment->end_time->format('H:i:s'),
                    'status' => $appointment->status,
                    'notes' => $appointment->notes
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
