<?php

namespace App\Repositories\EmployeeSchedule;

use App\Enums\AppointmentStatus;
use App\Enums\ScheduleStatus;
use App\Models\EmployeeSchedule;
use App\Repositories\BaseRepository;
use Carbon\Carbon;

class EmployeeScheduleRepository extends BaseRepository implements EmployeeScheduleRepositoryInterface{

    public function __construct(EmployeeSchedule $employeeSchedule){
        parent::__construct($employeeSchedule);
    }

    public function filters($filters)
    {
        
    }

    public function softDelete($id)
    {
        
    }

    public function getAvailableEmployee(int $slotId){
        $bookedEmployeeIds = EmployeeSchedule::where('slot_id',$slotId)
            ->where('status','booked')
            ->pluck('employee_id')
            ->toArray();

        return EmployeeSchedule::whereNotIn('employee_id',$bookedEmployeeIds)
            ->where('is_active',1)
            ->get();
    }

    public function assginEmployeeToSlot(string $employeeId, int $slotId, int|null $appointmentId = null){
        return EmployeeSchedule::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'slot_id' => $slotId
            ],
            [
                'appointment_id' => $appointmentId,
                'status' => $appointmentId ? ScheduleStatus::Booked : ScheduleStatus::Available
            ]
        );
    }

    public function realeaseEmployeeFromSlot(string $employeeId, int $slotId){
        return EmployeeSchedule::where('employee_id', $employeeId)
            ->where('slot_id',$slotId)
            ->update([
                'appointment_id' => null,
                'status' => ScheduleStatus::Available
            ]);
    }

    public function getEmployeeSchedulesByDate(string $employeeId, string $date){
        $date = Carbon::parse($date);
        return EmployeeSchedule::where('employee_id',$employeeId)
            ->whereHas('timeSlot', function($query) use ($date){
                $query->where('slot_date',$date->format('Y-m-d'));
            })
            ->with('timeSlot')
            ->orderBy('created_at')
            ->get();
    }

    public function isEmployeeAvailable(string $employeeId, int $slotId){
        $schedule = EmployeeSchedule::where('employee_id', $employeeId)
             ->where('slot_id',$slotId)
             ->first();
        
        return !$schedule || $schedule->status === ScheduleStatus::Available;
    }
}