<?php

namespace App\Repositories\TimeSlot;

use App\Repositories\RepositoryInterface;

interface TimeSlotRepositoryInterface extends RepositoryInterface{
    public function generateTimeSlots(string $date);
    public function generateTimeSlotsDb(string $date);
    public function getAvailableTimeSlots(string $date);
    public function getTimeSlotForDateRange(string $startDate, string $endDate);

    
}