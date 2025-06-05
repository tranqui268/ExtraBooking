<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'vehicle_id',
        'service_id',
        'last_maintenance_date',
        'next_maintenance_date',
        'maintenance_interval',
        'mileage_interval',
        'current_mileage',
        'status',
        'priority',
        'notes'
    ];

    public function vehicle(){
        return $this->belongsTo(Vehicle::class,'vehicle_id');
    }

    public function service(){
        return $this->belongsTo(Service::class,'service_id');
    }

    public function notifications(){
        return $this->hasMany(Notification::class,'maintenance_schedule_id');
    }
}
