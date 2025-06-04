<?php

namespace App\Http\Controllers;

use App\Repositories\WorkingHour\WorkingHourRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WorkingHourController extends Controller
{
    protected $workingHourRepo;

    public function __construct(WorkingHourRepositoryInterface $workingHourRepo){
        $this->workingHourRepo = $workingHourRepo;
    }

    public function getWorkingHourByDate(Request $request){
        $date = Carbon::parse($request->input('date'));
        $dayOfWeek = $date -> dayOfWeekIso;
        
        $data = $this->workingHourRepo->getWorkingHoursByDay($dayOfWeek);
        if ($data) {
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Không có dữ liệu'
        ]);
    }
}
