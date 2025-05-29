<?php

namespace App\Repositories\Appointment;

use App\Models\User;
use App\Repositories\RepositoryInterface;

interface AppointmentRepositoryInterface extends RepositoryInterface{
    public function getAppointmentsByDate(string $date);
    public function getAppointmentsByEmployee(string $employeeId, string $date);
    public function getAppointmentsByCustomer(int $customerId);
    public function getAppointmentsUser($user,string $view, $date);
    public function updateStatus(int $appointmentId, string $status);
    public function getConflictingAppointments(string $employeeId, string $date, string $startTime, string $endTime);
}