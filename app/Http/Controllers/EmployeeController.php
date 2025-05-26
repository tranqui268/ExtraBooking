<?php

namespace App\Http\Controllers;

use App\Repositories\Employee\EmployeeRepositoryInterface;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $employeeRepo;

    public function __construct(EmployeeRepositoryInterface $employeeRepo){
        $this->employeeRepo = $employeeRepo;
    }

    public function index(){
        return view('employee.index');
    }

    public function getAll(){
        $result = $this->employeeRepo->getAll();
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
