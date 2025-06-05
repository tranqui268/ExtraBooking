<?php

namespace App\Repositories\TimeSlot;

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
            
            $startTime->addMinutes(20);
        }

        $slotAvailable = $this->getAvailableTimeSlots($selectedDate);

        if (count($slotAvailable) > 0) {
            foreach ($timeSlots as $i => $slot){
                $slotTime = Carbon::parse($slot['time']);
                $slotStart = $slotTime->copy()->startOfHour();
                $slotEnd = $slotTime->copy()->endOfHour();

                $hasAvailableSlot = false;
                foreach ($slotAvailable as $available) {
                    $availableTime = Carbon::parse($available->start_time);
                    $availableTime = $availableTime->startOfMinute();
                    if ($availableTime->between($slotStart, $slotEnd)) {
                        $hasAvailableSlot = true;
                        break;
                    }
                }

                if (!$hasAvailableSlot) {
                    $timeSlots[$i]['disabled'] = true;
                }

            }
        }

        return $timeSlots;
    }


    public function generateTimeSlotsDb(string $date){
        $date = Carbon::parse($date);
        $now = Carbon::now();
        $isToday = $date->isSameDay($now);

        if ($date->isBefore(Carbon::today())) {
            return;
        }

        $dayOfWeek = $date->dayOfWeekIso;

        $workingHour = WorkingHour::where('day_of_week',$dayOfWeek)->first();

        if (!$workingHour || !$workingHour->is_working_day) {
            return;
        }

        if ($isToday) {
            if (Carbon::now()->greaterThan($workingHour->end_time)) {
                return;
            }
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
           ->where(function($query) {
            $query->whereHas('employeeSchedules', function($subQuery) {
                $subQuery->where('status', 'booked');
            }, '<', 2)
            ->orWhereDoesntHave('employeeSchedules');
        })
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