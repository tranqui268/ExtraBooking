<?php

namespace App\Http\Controllers;

use App\Repositories\Vehicle\VehicleRepositoryInterface;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    protected $vehicleRepo;

    public function __construct(VehicleRepositoryInterface $vehicleRepo){
        $this->vehicleRepo = $vehicleRepo;
    }

    public function getByCustomer($customerId){
        try {
            $vehicle = $this->vehicleRepo->getByCustomer($customerId);
            return response()->json([
                'success' => true,
                'message' => 'Lấy dữ liệu thành công',
                'data' => $vehicle
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server' . $e->getMessage()
            ],500);
        }
    }

    public function vehicleLookup(Request $request){
        try {
            $vehicle = $this->vehicleRepo->vehicleLookup($request);
            return response()->json([
                'success' => true,
                'message' => 'Lấy dữ liệu thành công',
                'data' => $vehicle
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server' . $e->getMessage()
            ],500);
        }
    }
}
