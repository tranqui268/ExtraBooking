<?php

namespace App\Http\Controllers;

use App\Repositories\Part\PartRepositoryInterface;
use Illuminate\Http\Request;

class PartController extends Controller
{
    protected $partRepo;

    public function __construct(PartRepositoryInterface $partRepo){
        $this->partRepo = $partRepo;
    }

    public function getAll(){
        try {
            $parts = $this->partRepo->getAll();
            return response()->json([
                'sucess' => true,
                'message' => 'Lấy dữ liệu thành công',
                'data' => $parts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'message' => 'Lỗi hệ thống' . $e->getMessage(),
            ],500);
            
        }

    }
}
