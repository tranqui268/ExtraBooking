<?php

namespace App\Repositories\TimeSlot;

use App\Models\Service;
use App\Models\TimeSlot;
use App\Models\WorkingHour;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TimeSlotRepository extends BaseRepository implements TimeSlotRepositoryInterface{
    protected $slotInterval = 20;

    public function __construct(TimeSlot $timeSlot){
        parent::__construct($timeSlot);
    }

    public function filters($filters)
    {
        
    }

    public function generateTimeSlots(string $selectedDate){
        $now = Carbon::now();
        $date = Carbon::parse($selectedDate);
        $currentDate = $now->toDateString();
        $isToday = $date->isSameDay($now);
        $currentTime = $now;

        Log::debug('TimeSlotService: generateTimeSlots', [
            'selectedDate' => $selectedDate,
            'now' => $now->toDateTimeString(),
            'isToday' => $isToday,
        ]);

        $dayOfWeek = $date -> dayOfWeekIso;

        $workingHours = WorkingHour::where('day_of_week', $dayOfWeek)->first();

        Log::debug('Working Hours', [
            'dayOfWeek' => $dayOfWeek,
            'workingHours' => $workingHours ? $workingHours->toArray() : null,
        ]);

        $timeSlots = [];

        if (!$workingHours || !$workingHours->is_working_day){
            return $timeSlots;
        }
        
        $startTime = Carbon::parse($workingHours->start_time);
        $endTime = Carbon::parse($workingHours->end_time);

        while($startTime->lessThan($endTime)){
            $time = $startTime->format('H:i');

            $disabled = $isToday && $startTime->lessThan($currentTime);

            $timeSlots[] = [
                'time' => $time,
                'disabled' => $disabled,
            ];
            
            $startTime->addMinutes(60);
        }
        return $timeSlots;
    }


    public function generateTimeSlotsDb(string $date){
        $date = Carbon::parse($date);
        $dayOfWeek = $date->dayOfWeekIso;

        $workingHour = WorkingHour::where('day_of_week',$dayOfWeek)->first();

        if (!$workingHour || !$workingHour->is_working_day) {
            return;
        }

        $startTime = Carbon::parse($workingHour->start_time);
        $endTime = Carbon::parse($workingHour->end_time);

        $slots = [];
        while ($startTime->lessThan($endTime)) {
            $slot = TimeSlot::firstOrCreate(
                [
                    'slot_date' => $date->toDateString(),
                    'start_time' => $startTime->toTimeString()
                ],
                [
                    'end_time' => $startTime->copy()->addMinutes($this->slotInterval)->toTimeString(),
                    'max_employees' => 2
                ]
            );
            $slots[] = [
                'id' => $slot->id,
                'slot_date' => $slot->slot_date,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'max_employees' => $slot->max_employees,
            ];
            $startTime->addMinutes($this->slotInterval);
        }
        return $slots;     
    }

    public function getAvailableTimeSlots(string $date){
        $date = Carbon::parse($date);
        return TimeSlot::where('slot_date', $date->format('Y-m-d'))
           ->whereHas('employeeSchedules', function($query){
               $query->where('status','available');
           },'<',2)
           ->orWhereDoesntHave('employeeSchedules')
           ->orderBy('start_time')
           ->get();
    }

    public function getTimeSlotForDateRange(string $startDate, string $endDate){
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        return TimeSlot::whereBetween('slot_date',[
            $startDate->format('Y-d-m'),
            $endDate->format('Y-m-d')
        ])->orderBy('slot_date')->orderBy('start_time')->get();
    }

    public function softDelete($id)
    {
        
    }
}