<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeSlot extends Model
{
    use HasFactory;

    protected $table = 'time_slots';

    protected $fillable = ['id','slot_date','start_time','end_time','max_employees'];

    protected $casts = [
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
    ];

    public function employeeSchedules() : HasMany {
        return $this->hasMany(EmployeeSchedule::class,'slot_id');
    }

    public function appointments() : HasMany {
        return $this->hasMany(Appointment::class, 'slot_id');
    }
}
