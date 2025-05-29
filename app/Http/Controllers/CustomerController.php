<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Repositories\Customer\CustomerRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    protected $customerRepo;

    public function __construct(CustomerRepositoryInterface $customerRepo){
        $this->customerRepo = $customerRepo;
    }

    public function index(){
        return view('customer.index');
    }

    public function getAllCustomer(Request $request){
        $customers = $this->customerRepo->filters($request);
        return response()->json([
            'data' => $customers->items(),
            'pagination' => [
                'total' => $customers->total(),
                'page_size' => $customers->perPage(),
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
            ]
        ]);
    }

    public function create(StoreCustomerRequest $request){
        Log::info('Creating customer', ['data' => $request->validated()]);
        $result = $this->customerRepo->create($request->validated());
        return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Tạo khách hàng thành công'
        ], 201);
    }
}
