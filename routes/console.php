<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('maintenance:send-reminders {--type=upcoming} {--days=7}', function() {
    $type = $this->option('type');
    $days = (int) $this->option('days');
    
    $this->info("Dispatching maintenance reminders job...");
    $this->info("Type: {$type}, Days ahead: {$days}");

    \App\Jobs\SendMaintenanceRemindersJob::dispatch($type,$days);

    $this->info("Maintenance reminders job has been queued successfully!");
})->describe('Send maintenance reminder notifications');
