<?php

namespace App\Console\Commands;

use App\Jobs\SendMaintenanceRemindersJob;
use Illuminate\Console\Command;

class SendMaintenanceReminders extends Command
{
    protected $signature = 'maintenance:send-reminders 
                          {--type=upcoming : Type of reminder (upcoming, overdue, urgent)}
                          {--days=7 : Days ahead for upcoming reminders}';

    protected $description = 'Send maintenance reminder notifications';

    public function handle()
    {
        $type = $this->option('type');
        $days = (int) $this->option('days');

        $this->info("Dispatching maintenance reminders job...");
        $this->info("Type: {$type}, Days ahead: {$days}");

        SendMaintenanceRemindersJob::dispatch($type,$days);

        $this->info("Maintenance reminders job has been queued successfully!");
    }


}
