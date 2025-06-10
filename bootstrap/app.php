<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
       $middleware->alias([
          'require.api.token' => \App\Http\Middleware\RequireApiToken::class
       ]);

       $middleware->api(prepend: [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \App\Http\Middleware\AddTokenToHeaderMiddleware::class
       ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->withSchedule(function (Schedule $schedule){
        $schedule->command('maintenance:send-reminders --type=upcoming --days=7')
                 ->dailyAt('08:00')
                 ->withoutOverlapping()
                 ->runInBackground();
        
        $schedule->command('maintenance:send-reminders --type=upcoming --days=1')
                 ->dailyAt('06:00')
                 ->withoutOverlapping()
                 ->runInBackground();
        
        $schedule->command('maintenance:send-reminders --type=overdue')
                 ->twiceDaily(9, 15)
                 ->withoutOverlapping()
                 ->runInBackground();
    })
    ->create();
