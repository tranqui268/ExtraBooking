<?php

namespace App\Repositories\Appointment;

use App\Models\Appointment;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

    public function getAppointmentsByCustomer(int $customerId){
        return Appointment::where('customer_id',$customerId)
             ->orderBy('id','desc')
             ->get();
    }

    public function getAppointmentsUser($user, string $view, $date){
        Log::info($user->customer->id);
        $query = Appointment::where('customer_id',$user->customer->id);

        if ($view === 'week') {
            $startOfWeek = Carbon::parse($date)->startOfWeek();
            $endOfWeek = Carbon::parse($date)->endOfWeek();
            $query->whereBetween('appointment_date',[$startOfWeek,$endOfWeek]);
        }

        $appointments = $query->with(['customer','service'])
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->get()
            ->map(function($appointment){
                return [
                    'id' => $appointment->id,
                    'customer_name' => $appointment->customer->name,
                    'service' => $appointment->service->service_name,
                    'date' => $appointment->appointment_date,
                    'start_time' => $appointment->start_time->format('H:i:s'),
                    'end_time' => $appointment->end_time->format('H:i:s'),
                    'notes' => $appointment->notes
                ];

            });
        
        return $appointments;
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