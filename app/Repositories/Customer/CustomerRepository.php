<?php

namespace App\Repositories\Customer;

use App\Models\Appointment;
use App\Models\Customer;
use App\Repositories\BaseRepository;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface{

    public function __construct(Customer $customer){
        parent::__construct($customer);

    }

    public function filters($filters)
    {
        $query = $this->model->query();

        if(!empty($filters['name'])){
            $query->where('name','like','%'. $filters['name'] .'%');
        }

        if(!empty($filters['email'])){
            $query->where('email','like','%'. $filters['email'] .'%');
        }

        if(isset($filters['status']) && $filters['status'] !== null){
            $query->where('is_delete', $filters['status']);
        }

        if(!empty($filters['address'])){
            $query->where('address','like','%'. $filters['address'] .'%');
        }

        $query->addSelect([
            'appointment_count' => Appointment::selectRaw('COUNT(*)')
                ->whereColumn('customer_id', 'customers.id'),
            'total_amount' => Appointment::selectRaw('COALESCE(SUM(total_amount), 0)')
                ->whereColumn('customer_id', 'customers.id')
                ->where('status', 'confirmed'), // chỉ tính appointment hoàn thành completed
            'last_appointment_date' => Appointment::select('appointment_date')
                ->whereColumn('customer_id', 'customers.id')
                ->orderBy('appointment_date', 'desc')
                ->limit(1)
        ]);
       
        return $query->orderBy('id','desc')->paginate($filters['perPage'] ?? 6);

    }

    public function softDelete($id)
    {
        $customer = $this->model->findOrFail($id);
        $customer -> updatate(['is_delete' => 1]);
        return true;
    }

    public function getCustomerByUserId($userId){
        return Customer::where('user_id',$userId)->first();
    }


}