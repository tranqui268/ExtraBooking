<?php

namespace App\Models;

use App\Enums\ScheduleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'employee_id',
        'slot_id',
        'appointment_id',
        'status'
    ];

    protected $casts = [
        'status' => ScheduleStatus::class,
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class, 'slot_id', 'id');
    }


    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'id');
    }
}
