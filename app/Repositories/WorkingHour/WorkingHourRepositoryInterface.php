<?php

namespace App\Repositories\WorkingHour;

use App\Repositories\RepositoryInterface;

interface WorkingHourRepositoryInterface extends RepositoryInterface{
    public function getWorkingHoursByDay(int $dayOfWeek);

    public function isWorkingDay(int $dayOfWeek);

    public function getWorkingDay() : array;
}