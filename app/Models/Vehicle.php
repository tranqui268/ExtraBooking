<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'customer_id',
        'license_plate',
        'brand',
        'model',
        'year_manufactory',
        'engine_number',
        'chassis_number',
        'fuel_type',
        'is_active'
    ];

    public function customer(){
        return $this->belongsTo(Customer::class,'customer_id');
    }

    public function maintenanceSchedules(){
        return $this->hasMany(MaintenanceSchedule::class,'vehicle_id');
    }
}
