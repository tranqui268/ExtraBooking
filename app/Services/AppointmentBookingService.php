<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\EmployeeSchedule;
use App\Models\Service;
use App\Repositories\Appointment\AppointmentRepositoryInterface;
use App\Repositories\Customer\CustomerRepositoryInterface;
use App\Repositories\EmployeeSchedule\EmployeeScheduleRepositoryInterface;
use App\Repositories\MaintenanceSchedule\MaintenanceScheduleRepositoryInterface;
use App\Repositories\TimeSlot\TimeSlotRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppointmentBookingService{
    protected $timeSlotRepo;
    protected $appoitmentRepo;
    protected $employeeScheduleRepo;
    protected $customerRepo;
    protected $maintenanceRepo;

    public function __construct(
        TimeSlotRepositoryInterface $timeSlotRepo,
        AppointmentRepositoryInterface $appointmentRepo,
        EmployeeScheduleRepositoryInterface $employeeScheduleRepo,
        CustomerRepositoryInterface $customerRepo,
        MaintenanceScheduleRepositoryInterface $maintenanceRepo
    ){
        $this->timeSlotRepo = $timeSlotRepo;
        $this->appoitmentRepo = $appointmentRepo;
        $this->employeeScheduleRepo = $employeeScheduleRepo;
        $this->customerRepo = $customerRepo;
        $this->maintenanceRepo = $maintenanceRepo;
    }

    public function bookAppointment(array $bookingData){
        
        Log::info('request',['data' => $bookingData]);
        return DB::transaction(function() use ($bookingData){
            $startTimeCarbon = Carbon::parse($bookingData['start_time']);
            $appointmentDate = Carbon::parse($bookingData['appointment_date']);
            $now = Carbon::now();

            if ($appointmentDate->isBefore(Carbon::today())) {
                throw new \Exception('Không được đặt ngày trong quá khứ');
            }

            $isToday = $appointmentDate->isSameDay($now);
            if ($isToday) {
                if ($startTimeCarbon->lessThan($now)) {
                    throw new \Exception('Không được đặt thời gian trong quá khứ');
                }
            }

            
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

           
            $minBookingTime = $now->copy()->addMinutes(30);
            $limitTime = $startTimeCarbon->copy()->addHour();

            if($startTime->gt($limitTime)){
                throw new \Exception('Khung giờ này đã hết lịch phù hợp');
            }

            if ($isToday) {
                if ($startTime->lt($minBookingTime)) {
                    throw new \Exception('Cần đặt lịch trước ít nhất 30 phút so với giờ bắt đầu.');
                }
            }

            $customer = Customer::find($bookingData['customer_id']);

            if(!$customer){
                throw new \Exception('Khách hàng không tồn tại');
            }

            $vehicle = $customer->vehicle()->first();
            Log::info('Vehicle',['data' => $vehicle]);

            if (!$vehicle) {
                throw new \Exception('Khách hàng chưa khai báo xe trên hệ thống');
            }

            if ($this->hasConflictingAppointment(
                $customer->id, 
                $appointmentDate->format('Y-m-d'), 
                $startTime, $endTime
                )
            ) {
                throw new \Exception('Bạn đã có lịch hẹn trong khung thời gian này. Vui lòng chọn thời gian khác.');
            }

            $appointmentData = [
                'customer_id' => $customer->id,
                'vehicle_id' => $vehicle->id,
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

            $maintenanceInterval = $service->maintenance_interval;
            $nextMaintenance = $appointmentDate->copy()->addMonths($maintenanceInterval)->format('Y-m-d');

            $maintenanceSchedules = [
                'vehicle_id' => $appointment->vehicle_id,
                'service_id' => $service->id,
                'last_maintenance_date' => $appointment->appointment_date,
                'next_maintenance_date' => $nextMaintenance,
                'maintenance_interval' => $maintenanceInterval,
                'mileage_interval' => 1000,
                'current_mileage' => 500,
                'notes' => 'Bảo dưỡng định kỳ'
            ];
            $this->maintenanceRepo->create($maintenanceSchedules);



            return $appointment;

        });
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
        $availableEmployees = null;
        
        foreach ($slots as $slot) {
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
                $availableEmployees = $availableEmployees->filter(function($employee) use ($slotAvailableEmployees) {
                    return $slotAvailableEmployees->contains('id', $employee->id);
                });
            }
            
            if ($availableEmployees && $availableEmployees->count() == 0) {
                return null;
            }
        }

        // return $availableEmployees && $availableEmployees->count() > 0 ? $availableEmployees->first() : null;
        return $this->selectBestEmployee($availableEmployees,$slots);
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

    private function addTimeConflictConditions($query, $startTime, $endTime){
        $query->where(function($q) use ($startTime, $endTime) {
            $q->where('start_time', '<=', $startTime)
            ->where('end_time', '>', $startTime);
        })->orWhere(function($q) use ($startTime, $endTime) {
            $q->where('start_time', '<', $endTime)
            ->where('end_time', '>=', $endTime);
        })->orWhere(function($q) use ($startTime, $endTime) {
            $q->where('start_time', '>=', $startTime)
            ->where('end_time', '<=', $endTime);
        });
    }

    private function hasConflictingAppointment($customerId, $appointmentDate, $startTime, $endTime){
        return DB::table('appointments')
            ->where('customer_id', $customerId)
            ->where('appointment_date', $appointmentDate)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($startTime, $endTime) {
                $this->addTimeConflictConditions($query, $startTime, $endTime);
            })
            ->exists(); 
    }

    private function hasConflictingAppointment1($customerId, $appointmentDate, $startTime, $endTime){
        return DB::table('appointments')
            ->where('customer_id', $customerId)
            ->where('appointment_date', $appointmentDate)
            ->where('status', '!=', 'cancelled')         
            ->exists(); 
    }

    protected function getSlotDate($slot){
        if (is_object($slot)) {
            return $slot->slot_date ?? $slot->date ?? now()->format('Y-m-d');
        } elseif (is_array($slot)) {
            return $slot['slot_date'] ?? $slot['date'] ?? now()->format('Y-m-d');
        }
        
        return now()->format('Y-m-d');
    }

    protected function selectBestEmployee($availableEmployees, $slots){
        try {
            $slotDate = $this->getSlotDate($slots[0]);
            
            Log::info('Selecting best employee', [
                'available_count' => $availableEmployees->count(),
                'slot_date' => $slotDate
            ]);
            
            $employeesWithWorkload = $availableEmployees->map(function($employee) use ($slotDate) {
                $dailyWorkload = EmployeeSchedule::where('employee_id', $employee->id)
                    ->whereHas('timeSlot', function($query) use ($slotDate) {
                        $query->where('slot_date', $slotDate);
                    })
                    ->where('status', 'booked')
                    ->count();
                    
                
                $weeklyWorkload = EmployeeSchedule::where('employee_id', $employee->id)
                    ->whereHas('timeSlot', function($query) use ($slotDate) {
                        $startOfWeek = Carbon::parse($slotDate)->startOfWeek();
                        $endOfWeek = Carbon::parse($slotDate)->endOfWeek();
                        $query->whereBetween('slot_date', [$startOfWeek, $endOfWeek]);
                    })
                    ->where('status', 'booked')
                    ->count();
                    
                $employee->daily_workload = $dailyWorkload;
                $employee->weekly_workload = $weeklyWorkload;
                
                Log::info('Employee workload', [
                    'employee_id' => $employee->id,
                    'name' => $employee->name ?? 'Unknown',
                    'daily_workload' => $dailyWorkload,
                    'weekly_workload' => $weeklyWorkload
                ]);
                
                return $employee;
            });
            
            
            $selectedEmployee = $employeesWithWorkload
                ->sortBy([
                    ['daily_workload', 'asc'],      
                    ['weekly_workload', 'asc'],     
                    [function() { return rand(0, 100); }, 'asc'] 
                ])
                ->first();
                
            Log::info('Selected employee', [
                'employee_id' => $selectedEmployee->id,
                'name' => $selectedEmployee->name ?? 'Unknown',
                'daily_workload' => $selectedEmployee->daily_workload,
                'weekly_workload' => $selectedEmployee->weekly_workload
            ]);
            
            return $selectedEmployee;
            
        } catch (\Exception $e) {
            Log::error('Error in selectBestEmployee', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $availableEmployees->first();
        }
    }






}