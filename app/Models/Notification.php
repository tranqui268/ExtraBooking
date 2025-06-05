<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'maintenance_schedule_id',
        'type',
        'title',
        'message',
        'send_via',
        'scheduled_time',
        'send_time',
        'status'
    ];

    public function maintenanceSchedule(){
        return $this->belongsTo(MaintenanceSchedule::class,'maintenance_schedule_id');
    }
}
