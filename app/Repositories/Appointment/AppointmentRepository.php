<?php

namespace App\Repositories\Appointment;

use App\Models\Appointment;
use App\Repositories\BaseRepository;
use Carbon\Carbon;

class AppointmentRepository extends BaseRepository implements AppointmentRepositoryInterface{

    public function __construct(Appointment $appointment){
        parent::__construct($appointment);
    }

    public function filters($filters)
    {
        
    }

    public function softDelete($id)
    {
        
    }

    public function getAppointmentsByDate(string $date){
        $date = Carbon::parse($date);
        return Appointment::where('appointment_date',$date->format('Y-m-d'))
             ->with(['customer','service','employee'])
             ->orderBy('start_time')
             ->get();
    }

    public function getAppointmentsByEmployee(string $employeeId, string $date){
        $date = Carbon::parse($date);
        return Appointment::where('appointment_date',$date->format('Y-m-d'))
             ->where('employee_id', $employeeId)
             ->where('status','!=','cancelled')
             ->orderBy('start_time')
             ->get();
    }

    public function updateStatus(int $appointmentId, string $status){
        return Appointment::where('id',$appointmentId)
           ->update([
            'status' => $status
           ]);
    }

    public function getConflictingAppointments(string $employeeId, string $date, string $startTime, string $endTime){
        $date = Carbon::parse($date);
        return Appointment::where('employee_id',$employeeId)
           ->where('appointment_date',$date->format('Y-m-d'))
           ->where('status','!=','cancelled')
           ->where(function($query) use ($startTime,$endTime){
               $query->whereBetween('start_time',[$startTime,$endTime])
               ->orWhereBetween('end_time',[$startTime,$endTime])
               ->orWhere(function($q) use ($startTime,$endTime){
                   $q->where('start_time','<=',$startTime)
                     ->where('end_time', '>=', $endTime);
               });

           })
           ->get();

    }
}