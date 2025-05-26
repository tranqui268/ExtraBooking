<?php

namespace App\Repositories\EmployeeSchedule;

use App\Repositories\RepositoryInterface;

interface EmployeeScheduleRepositoryInterface extends RepositoryInterface{
    public function getAvailableEmployee(int $slotId);
    public function assginEmployeeToSlot(string $employeeId, int $slotId, ?int $appointmentId = null);
    public function realeaseEmployeeFromSlot(string $employeeId, int $slotId);
    public function getEmployeeSchedulesByDate(string $employeeId, string $date);
    public function isEmployeeAvailable(string $employeeId, int $slotId);
}