<?php

namespace App\Models;

use Carbon\Carbon;
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

    // Scopes
    public function scopePending($query){
        return $query->where('status','pending');
    }

    public function scopeDueForReminder($query, $days = 7)
    {
        return $query->where('next_maintenance_date', '<=', Carbon::now()->addDays($days))
                    ->whereIn('status', ['pending', 'notified']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('next_maintenance_date', '<', Carbon::now())
                    ->where('status', '!=', 'completed');
    }

    public function isOverdue(): bool
    {
        return $this->next_maintenance_date < Carbon::now();
    }

    public function getDaysUntilMaintenance(): int
    {
        return Carbon::now()->diffInDays($this->next_maintenance_date, false);
    }
}
