<?php

namespace App\Http\Controllers;

use App\Repositories\TimeSlot\TimeSlotRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TimeSlotController extends Controller
{

    protected $timeSlotRepo;

    public function __construct(TimeSlotRepositoryInterface $timeSlotRepo){
        $this->timeSlotRepo = $timeSlotRepo;
    }

    public function generateTimeSlot(Request $request){
        $date = $request->input('date',now()->toDateString());
        $result = $this->timeSlotRepo->generateTimeSlots($date);
        return response()->json([
            'success' => true,
            'message' => "Tạo time slot cho ngày $date",
            'data' => $result
        ]);
    }

    public function generateTimeSlotDb(Request $request){
        $date = $request->input('date');
        $result = $this->timeSlotRepo->generateTimeSlotsDb($date);
        return response()->json([
            'success' => true,
            'message' => 'Tạo time slot cho database',
            'data' => $result
        ]);
    }
}
