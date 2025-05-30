<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\EmployeeSchedule;
use App\Models\Service;
use App\Repositories\Appointment\AppointmentRepositoryInterface;
use App\Repositories\Customer\CustomerRepositoryInterface;
use App\Repositories\EmployeeSchedule\EmployeeScheduleRepositoryInterface;
use App\Repositories\TimeSlot\TimeSlotRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppointmentBookingService{
    protected $timeSlotRepo;
    protected $appoitmentRepo;
    protected $employeeScheduleRepo;
    protected $customerRepo;

    public function __construct(
        TimeSlotRepositoryInterface $timeSlotRepo,
        AppointmentRepositoryInterface $appointmentRepo,
        EmployeeScheduleRepositoryInterface $employeeScheduleRepo,
        CustomerRepositoryInterface $customerRepo
    ){
        $this->timeSlotRepo = $timeSlotRepo;
        $this->appoitmentRepo = $appointmentRepo;
        $this->employeeScheduleRepo = $employeeScheduleRepo;
        $this->customerRepo = $customerRepo;
    }

    public function bookAppointment(array $bookingData){
        Log::info('request',['data' => $bookingData]);
        return DB::transaction(function() use ($bookingData){
            $appointmentDate = Carbon::parse($bookingData['appointment_date']);
            $service = Service::find($bookingData['service_id']);

            if(!$service){
                throw new \Exception('Dịch vụ không tồn tại');
            }

            // Tính toán số slot cần thiết
            $serviceDuration = $service->duration;
            $slotNeeded = ceil($serviceDuration / 20);

            $availableSlot = $this -> timeSlotRepo ->generateTimeSlotsDb($bookingData['appointment_date']);

            // Tìm các slot liên tiếp
            $consecutiveSlots = $this->findConsecutiveAvailableSlots(
                $availableSlot,
                $bookingData['start_time'],
                $slotNeeded
            );
            Log::info('Slot trống: ', ['data' => $consecutiveSlots]);

            if (count($consecutiveSlots) < $slotNeeded) {
                throw new \Exception('Không có đủ slot liên tiếp trống');
            }

            $assignedEmployee = $this->findAvailableEmployeeForSlots($consecutiveSlots);
            Log::info('Nhân viên: ', ['data' => $assignedEmployee]);
            if (!$assignedEmployee) {
                throw new \Exception('Không có nhân viên rảnh trong khung giờ này');
            }

            $startTime = $consecutiveSlots[0]['start_time'];
            $endTime = $consecutiveSlots[count($consecutiveSlots)-1]['end_time'];

            $startTimeCarbon = Carbon::parse($bookingData['start_time']);
            $limitTime = $startTimeCarbon->copy()->addHour();

            if($startTime->gt($limitTime)){
                

            }

            $customer = Customer::find($bookingData['customer_id']);

            if(!$customer){
                throw new \Exception('Khách hàng không tồn tại');
            }

            $appointmentData = [
                'customer_id' => $customer->id,
                'service_id' => $bookingData['service_id'],
                'appointment_date' => $appointmentDate->format('Y-m-d'),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'employee_id' => $assignedEmployee->id,
                'total_amount' => $service->base_price,
                'status' => 'confirmed',
                'notes' => $bookingData['notes'] ?? null
            ];

            $appointment = $this->appoitmentRepo->create($appointmentData);

            // phân công nhân viên
            foreach ($consecutiveSlots as $slot) {                   
                    $slotId = null;
                    if (is_object($slot)) {
                        $slotId = $slot->slot_id ?? $slot->id ?? null;
                    } elseif (is_array($slot)) {
                        $slotId = $slot['slot_id'] ?? $slot['id'] ?? null;
                    }
                    
                    if ($slotId) {
                        $this->employeeScheduleRepo->assginEmployeeToSlot(
                            $assignedEmployee->id,
                            $slotId,
                            $appointment->id
                        );
                    }
            }

            return $appointment;

        });
    }

    private function findOrCreateCustomer(array $customerData)
    {
        return Customer::firstOrCreate(
            ['email' => $customerData['email']],
            $customerData 
        );
    }

    protected function findConsecutiveAvailableSlots($allSlots, $startTime, $slotsNeeded)
    {
        $startTimeCarbon = Carbon::parse($startTime);
        $consecutiveSlots = [];
        
        foreach ($allSlots as $slot) {
            // Lấy slot_id để truy vấn
            $slotId = null;
            $slotStartTime = null;
            $maxEmployees = 2;
            
            if (is_object($slot)) {
                $slotId = $slot->slot_id ?? $slot->id ?? null;
                $slotStartTime = $slot->start_time ?? null;
                $maxEmployees = $slot->max_employees ?? 2;
            } elseif (is_array($slot)) {
                $slotId = $slot['slot_id'] ?? $slot['id'] ?? null;
                $slotStartTime = $slot['start_time'] ?? null;
                $maxEmployees = $slot['max_employees'] ?? 2;
            }
            
            if (!$slotId || !$slotStartTime) {
                continue;
            }
            
            $slotStart = Carbon::parse($slotStartTime);
            
            if ($slotStart->gte($startTimeCarbon)) {
                // Truy vấn trực tiếp để kiểm tra số nhân viên đã được đặt
                $bookedEmployees = EmployeeSchedule::where('slot_id', $slotId)
                    ->where('status', 'booked')
                    ->count();
                
                if ($bookedEmployees < $maxEmployees) {
                    $consecutiveSlots[] = $slot;
                    
                    if (count($consecutiveSlots) >= $slotsNeeded) {
                        // Kiểm tra tính liên tiếp
                        if ($this->areSlotsConsecutive($consecutiveSlots)) {
                            return array_slice($consecutiveSlots, 0, $slotsNeeded);
                        }
                    }
                } else {
                    $consecutiveSlots = []; 
                }
            }
        }

        return $consecutiveSlots;
    }

    protected function areSlotsConsecutive($slots)
    {
        if (count($slots) <= 1) {
            return true;
        }

        for ($i = 1; $i < count($slots); $i++) {
            // Lấy thời gian từ slot trước và slot hiện tại
            $prevSlotEndTime = null;
            $currentSlotStartTime = null;
            
            // Xử lý slot trước
            if (is_object($slots[$i-1])) {
                $prevSlotEndTime = $slots[$i-1]->end_time ?? null;
            } elseif (is_array($slots[$i-1])) {
                $prevSlotEndTime = $slots[$i-1]['end_time'] ?? null;
            }
            
            // Xử lý slot hiện tại
            if (is_object($slots[$i])) {
                $currentSlotStartTime = $slots[$i]->start_time ?? null;
            } elseif (is_array($slots[$i])) {
                $currentSlotStartTime = $slots[$i]['start_time'] ?? null;
            }
            
            if (!$prevSlotEndTime || !$currentSlotStartTime) {
                return false;
            }
            
            $prevSlotEnd = Carbon::parse($prevSlotEndTime);
            $currentSlotStart = Carbon::parse($currentSlotStartTime);
            
            // Kiểm tra liên tiếp (end_time của slot trước = start_time của slot sau)
            if (!$prevSlotEnd->eq($currentSlotStart)) {
                return false;
            }
        }
        return true;
    }


     protected function findAvailableEmployeeForSlots($slots)
    {
        Log::info('findAvailableEmployeeForSlots');
        // Lấy danh sách nhân viên có thể làm việc trong tất cả các slot
        $availableEmployees = null;
        
        foreach ($slots as $slot) {
            // Lấy slot_id
            $slotId = null;
            if (is_object($slot)) {
                $slotId = $slot->slot_id ?? $slot->id ?? null;
            } elseif (is_array($slot)) {
                $slotId = $slot['slot_id'] ?? $slot['id'] ?? null;
            }
            
            if (!$slotId) {
                continue;
            }

            Log::info('slot_id' ,['data'=>$slotId]);
            
            $slotAvailableEmployees = $this->employeeScheduleRepo->getAvailableEmployee($slotId);
            Log::info('slotAvailableEmployees' ,['data'=>$slotAvailableEmployees]);
            
            if ($availableEmployees === null) {
                $availableEmployees = $slotAvailableEmployees;
            } else {
                // Lấy giao của các nhân viên có sẵn
                $availableEmployees = $availableEmployees->filter(function($employee) use ($slotAvailableEmployees) {
                    return $slotAvailableEmployees->contains('id', $employee->id);
                });
            }
            
            // Nếu không còn nhân viên nào có sẵn cho tất cả slot, thoát sớm
            if ($availableEmployees && $availableEmployees->count() == 0) {
                return null;
            }
        }

        return $availableEmployees && $availableEmployees->count() > 0 ? $availableEmployees->first() : null;
    }

    public function getAvailableTimeSlots(string $date){
        return $this->timeSlotRepo->getAvailableTimeSlots($date);
    }

    public function cancelAppointment(int $appointmentId){
        return DB::transaction(function() use ($appointmentId){
            $appointment = $this->appoitmentRepo->getById($appointmentId);

            if (!$appointment) {
                throw new \Exception('Không tìm thấy lịch hẹn');
            }

                   
            $now = Carbon::now();
            $isToday = Carbon::parse($appointment->appointment_date)->isSameDay($now);    

            if ($isToday) {
                $startDateTime = Carbon::parse($appointment->start_time);
                $minutesUntilStart = $now->diffInMinutes($startDateTime,false);

                Log::debug('Cancel Appointment Check', [
                    'appointmentId' => $appointmentId,
                    'startDateTime' => $startDateTime->toDateTimeString(),
                    'now' => $now->toDateTimeString(),
                    'minutesUntilStart' => $minutesUntilStart
                ]);

                if ($minutesUntilStart < 30) {
                    throw new \Exception('Không thể hủy lịch hẹn dưới 30 phút trước giờ bắt đầu');
                }
            }

            $this->appoitmentRepo->updateStatus($appointmentId,'cancelled');

            $appointmentDate = Carbon::parse($appointment->appointment_date);
            $slots = $this->timeSlotRepo->getAvailableTimeSlots($appointmentDate);

            foreach($slots as $slot){
                $this->employeeScheduleRepo->realeaseEmployeeFromSlot(
                    $appointment->employee_id,
                    $slot->id
                );
            }

            return true;

        });
    }


}