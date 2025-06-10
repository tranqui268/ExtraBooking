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

        $this->app->bind(
            \App\Repositories\Vehicle\VehicleRepositoryInterface::class,
            \App\Repositories\Vehicle\VehicleRepository::class
        );

        $this->app->bind(
            \App\Repositories\Part\PartRepositoryInterface::class,
            \App\Repositories\Part\PartRepository::class
        );

        $this->app->bind(
            \App\Repositories\RepairOrder\RepairOrderRepositoryInterface::class,
            \App\Repositories\RepairOrder\RepairOrderRepository::class
        );

        $this->app->bind(
            \App\Repositories\RepairOrderPart\RepairOrderPartRepositoryInterface::class,
            \App\Repositories\RepairOrderPart\RepairOrderPartRepository::class
        );

        $this->app->bind(
            \App\Repositories\Review\ReviewRepositoryInterface::class,
            \App\Repositories\Review\ReviewRepository::class
        );

        $this->app->bind(
            \App\Repositories\MaintenanceSchedule\MaintenanceScheduleRepositoryInterface::class,
            \App\Repositories\MaintenanceSchedule\MaintenanceScheduleRepository::class
        );
       
    }

    public function boot(): void
    {
       if ($this->app->runningInConsole()) {
        $this->commands([
            \App\Console\Commands\SendMaintenanceReminders::class,
        ]);
       }
    }
}
