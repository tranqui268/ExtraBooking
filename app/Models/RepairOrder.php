<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'appointment_id',
        'description',
        'diagnosis',
        'work_performed',
        'technician_notes',
        'labor_cost',
        'parts_cost',
        'total_cost',
    ];

    public function appointment(){
        return $this->belongsTo(Appointment::class,'appointment_id');
    }

    public function repairOrderPart(){
        return $this->hasMany(RepairOrderPart::class,'repair_order_id');
    }

    public function review(){
        return $this->hasMany(Review::class,'repair_order_id');
    }
}
