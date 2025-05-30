<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Service\ServiceRepositoryInterface::class,
            \App\Repositories\Service\ServiceRepository::class
        );

        $this->app->bind(
            \App\Repositories\Employee\EmployeeRepositoryInterface::class,
            \App\Repositories\Employee\EmployeeRepository::class
        );

        $this->app->bind(
            \App\Repositories\TimeSlot\TimeSlotRepositoryInterface::class,
            \App\Repositories\TimeSlot\TimeSlotRepository::class,         
        );

        $this->app->bind(
            \App\Repositories\WorkingHour\WorkingHourRepositoryInterface::class,
            \App\Repositories\WorkingHour\WorkingHourRepository::class
        );

        $this->app->bind(
            \App\Repositories\Appointment\AppointmentRepositoryInterface::class,
            \App\Repositories\Appointment\AppointmentRepository::class
        );

        $this->app->bind(
            \App\Repositories\EmployeeSchedule\EmployeeScheduleRepositoryInterface::class,
            \App\Repositories\EmployeeSchedule\EmployeeScheduleRepository::class
        );

        $this->app->bind(
            \App\Repositories\Customer\CustomerRepositoryInterface::class,
            \App\Repositories\Customer\CustomerRepository::class
        );

        
    }

    public function boot(): void
    {
       
    }
}
