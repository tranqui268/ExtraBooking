<?php

namespace App\Repositories\Appointment;

use App\Repositories\RepositoryInterface;

interface AppointmentRepositoryInterface extends RepositoryInterface{
    public function getAppointmentsByDate(string $date);
    public function getAppointmentsByEmployee(string $employeeId, string $date);
    public function updateStatus(int $appointmentId, string $status);
    public function getConflictingAppointments(string $employeeId, string $date, string $startTime, string $endTime);
}