<?php

namespace App\Http\Controllers;

use App\Services\RepairOrderService;
use Illuminate\Http\Request;

class RepairOrderController extends Controller
{
    protected $repairOrderService;

    public function __construct(RepairOrderService $repairOrderService){
        $this->repairOrderService = $repairOrderService;
    }

    public function createOrder(Request $request){
        try {
            $repairOrder = $this->repairOrderService->createRepairOrder($request);
             return response()->json([
                'success' => true,
                'data' => $repairOrder,
                'message' => 'Thêm phụ tùng thành công'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Thêm phụ tùng thất bại: ' . $e->getMessage()
            ], 400);
        }

    }
}
