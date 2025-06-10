<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $table = 'appointments';
    protected $fillable = [
        'id',
        'customer_id',
        'vehicle_id',
        'employee_id',
        'service_id',
        'appointment_date',
        'total_amount',
        'start_time',
        'end_time',
        'status',
        'notes'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'status' => AppointmentStatus::class,
    ];

    public function customer() : BelongsTo{
        return $this->belongsTo(Customer::class,'customer_id','id');
    }

    public function employee() : BelongsTo{
        return $this->belongsTo(Employee::class,'employee_id','id');
    }

    public function service() : BelongsTo{
        return $this->belongsTo(Service::class,'service_id','id');
    }

    public function repairOrder(){
        return $this->hasOne(RepairOrder::class,'appointment_id');
    }

    public function vehicle(){
        return $this->belongsTo(Vehicle::class,'vehicle_id','id');
    }
}
