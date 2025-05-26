<?php

use App\Enums\AppointmentStatus;
use App\Enums\ScheduleStatus;
use App\Models\Service;
use App\Repositories\Appointment\AppointmentRepositoryInterface;
use App\Repositories\EmployeeSchedule\EmployeeScheduleRepositoryInterface;
use App\Repositories\TimeSlot\TimeSlotRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentBookingService{
    protected $timeSlotRepo;
    protected $appoitmentRepo;
    protected $employeeScheduleRepo;

    public function __construct(
        TimeSlotRepositoryInterface $timeSlotRepo,
        AppointmentRepositoryInterface $appointmentRepo,
        EmployeeScheduleRepositoryInterface $employeeScheduleRepo
    ){
        $this->timeSlotRepo = $timeSlotRepo;
        $this->appoitmentRepo = $appointmentRepo;
        $this->employeeScheduleRepo = $employeeScheduleRepo;
    }

    public function bookAppointment(array $bookingData){
        return DB::transaction(function() use ($bookingData){
            $appointmentDate = Carbon::parse($bookingData['appointment_date']);
            $service = Service::find($bookingData['service_id']);

            if(!$service){
                throw new Exception('Dịch vụ không tồn tại');
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

            if (count($consecutiveSlots) < $slotNeeded) {
                throw new Exception('Không có đủ slot liên tiếp trống');
            }

            $assignedEmployee = $this->findAvailableEmployeeForSlots($consecutiveSlots);

            if (!$assignedEmployee) {
                throw new Exception('Không có nhân viên rảnh trong khung giờ này');
            }

            $appointmentData = [
                'customer_id' => $bookingData['customer_id'],
                'service_id' => $bookingData['service_id'],
                'appointment_date' => $appointmentDate->format('Y-m-d'),
                'start_time' => $bookingData['start_time'],
                'end_time' => Carbon::parse($bookingData['start_time'])->addMinutes($serviceDuration)->format('H:i:s'),
                'employee_id' => $assignedEmployee->employee_id,
                'total_amount' => $service->price,
                'status' => 'confirmed',
                'notes' => $bookingData['notes'] ?? null
            ];

            $appointment = $this->appoitmentRepo->create($appointmentData);

            // phân công nhân viên
            foreach($consecutiveSlots as $slot){
                $this->employeeScheduleRepo->assginEmployeeToSlot(
                    $assignedEmployee->id,
                    $slot->id,
                    $appointment->id
                );
            }

            return $appointment;

        });
    }

    protected function findConsecutiveAvailableSlots($allSlots, $startTime, $slotsNeeded){
        $startTimeCarbon = Carbon::parse($startTime);
        $consecutiveSlots = [];

        foreach($allSlots as $slot){
            $slotStart = Carbon::parse($slot->start_time);

            if ($slotStart->gte($startTimeCarbon)) {
                $bookedEmployees = $slot->employeeSchedules()
                  ->where('status',ScheduleStatus::Booked)
                  ->count();

                if ($bookedEmployees < $slot->max_employees) {
                    $consecutiveSlots[] = $slot;

                    if (count($consecutiveSlots) >= $slotsNeeded) {
                        if ($this->areSlotsConsecutive($consecutiveSlots)) {
                            return array_slice($consecutiveSlots,0,$slotsNeeded);
                        }                       
                    }
                }else{
                    $consecutiveSlots = [];
                }
            }
        }
        return $consecutiveSlots;
    }

    protected function areSlotsConsecutive($slots){
        for ($i=1; $i < count($slots) ; $i++) { 
            $prevSlotEnd = Carbon::parse($slots[$i-1]->end_time);
            $currentSlotStart = Carbon::parse($slots[$i]->start_time);

            if (!$prevSlotEnd->eq($currentSlotStart)) {
                return false;
            }
        }
        return true;
    }

    protected function findAvailableEmployeeForSlots($slots){
        $availableEmployees = null;

        foreach($slots as $slot){
            $slotAvailableEmployees = $this->employeeScheduleRepo->getAvailableEmployee($slot->id);

            if ($availableEmployees === null) {
                $availableEmployees = $slotAvailableEmployees;
            }else{
                $availableEmployees = $availableEmployees->filters(function($employee) use ($slotAvailableEmployees){
                    return $slotAvailableEmployees->contains('id',$employee->id);
                });
            }
        }
        return $availableEmployees ? $availableEmployees->first() : null;
    }

    public function getAvailableTimeSlots(string $date){
        return $this->timeSlotRepo->getAvailableTimeSlots($date);
    }

    public function cancelAppointment(int $appointmentId){
        return DB::transaction(function() use ($appointmentId){
            $appointment = $this->appoitmentRepo->getById($appointmentId);

            if (!$appointment) {
                throw new Exception('Không tìm thấy lịch hẹn');
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