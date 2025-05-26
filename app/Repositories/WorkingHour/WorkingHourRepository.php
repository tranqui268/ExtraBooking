<?php

namespace App\Repositories\WorkingHour;

use App\Models\WorkingHour;
use App\Repositories\BaseRepository;

class WorkingHourRepository extends BaseRepository implements WorkingHourRepositoryInterface{

    public function __construct(WorkingHour $workingHour){
        parent::__construct($workingHour);
    }

    public function filters($filters)
    {
        
    }

    public function softDelete($id)
    {
        
    }

    public function getWorkingHoursByDay(int $dayOfWeek){
        return WorkingHour::where('day_of_week', $dayOfWeek)
            ->where('is_working_day',1)
            ->first();
    }

    public function isWorkingDay(int $dayOfWeek){
        return WorkingHour::where('day_of_week',$dayOfWeek)
            ->where('is_working_day',1)
            ->exists();
    }

    public function getWorkingDay(): array{
        return WorkingHour::where('is_working_day',1)
            ->pluck('day_of_week')
            ->toArray();
    }
}