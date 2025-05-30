<?php

namespace App\Http\Controllers;

use App\Charts\ServiceUsageChart;
use App\Http\Requests\StoreServiceRequest;
use App\Repositories\Service\ServiceRepositoryInterface;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected $serviceRepo;

    public function __construct(ServiceRepositoryInterface $serviceRepo){
        $this->serviceRepo = $serviceRepo;
    }

    public function index(){
        return view('service.index');
    }

    public function showDashboard(){
        $chart = new ServiceUsageChart();
        return view('service.service-dashboard',compact('chart'));
    }

    public function create(StoreServiceRequest $request){
        try {
            $data = array_merge(
                $request->validated(),
                ['id' => $this->serviceRepo->generateId($request->input('service_name'))]
            );
                       
            $service = $this->serviceRepo->create($data);
           
            return response()->json([
                'success' => true,
                'data' => $service,
                'message' => 'Tạo dịch vụ thành công'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update($id,StoreServiceRequest $request){
         try {
            $service = $this->serviceRepo->update($id,$request->validated());
            return response()->json([
                'success' => true,
                'data' => $service,
                'message' => 'Cập nhật dịch vụ thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAll(){
        $result = $this->serviceRepo->getAll();
        if($result){
            return response()->json([
                'success' => true,
                'message' => 'Lấy dữ liệu thành công',
                'data' => $result
            ]);
        }
        return response()->json([
                'success' => false,
                'message' => 'Lấy dữ liệu thất bại'
        ]);
    }

    public function getWithFilters(Request $request){
        $result = $this->serviceRepo->filters($request);
        if ($result) {
             return response()->json([
                'success' => true,
                'message' => 'Lấy dữ liệu thành công',
                'data' => $result
            ]);
        }
        return response()->json([
                'success' => false,
                'message' => 'Lấy dữ liệu thất bại'
        ]);
    }
}
