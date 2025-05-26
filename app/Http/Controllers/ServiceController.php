<?php

namespace App\Http\Controllers;

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
}
