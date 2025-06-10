<?php

namespace App\Jobs;

use App\Models\MaintenanceSchedule;
use App\Notifications\MaintenanceReminderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendMaintenanceRemindersJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    protected $reminderType;
    protected $dayAhead;

    public function __construct($reminderType = 'upcoming', $dayAhead = 7)
    {
        $this->reminderType = $reminderType;
        $this->dayAhead = $dayAhead;
    }

    public function handle(): void
    {
        Log::info("Starting maintenance reminders job",[
            'type' => $this->reminderType,
            'days_ahead' => $this->dayAhead
        ]);

        $schedules = $this->getSchedulesToNotify();
        $sentCount = 0;

        foreach ($schedules as $schedule) {
            try {
                $customer = $schedule->vehicle->customer;

                if ($customer && $customer->email) {
                    $customer->notify(new MaintenanceReminderNotification($schedule, $this->reminderType));

                    $this->updateScheduleStatus($schedule);
                    $sentCount++;
                    
                    Log::info("Sent maintenance reminder", [
                        'schedule_id' => $schedule->id,
                        'vehicle' => $schedule->vehicle->license_plate,
                        'owner_email' => $customer->email
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Failed to send maintenance reminder", [
                    'schedule_id' => $schedule->schedule_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info("Completed maintenance reminders job", [
            'sent_count' => $sentCount,
            'total_schedules' => $schedules->count()
        ]);
    }

    private function getSchedulesToNotify(){
        $query = MaintenanceSchedule::with(['vehicle.customer','service']);

        switch ($this->reminderType) {
            case 'overdue':
                return $query->overdue()->get();
            
            case 'urgent':
                return $query->dueForReminder(1)
                            ->where('priority','urgent')
                            ->get();

            case 'upcoming':
            
            default:
                return $query->dueForReminder($this->dayAhead)->get();
        }
    }

    private function updateScheduleStatus($schedule)
    {
        if ($schedule->isOverdue()) {
            $schedule->update(['status' => 'overdue']);
        } else {
            $schedule->update(['status' => 'notified']);
        }
    }
}
