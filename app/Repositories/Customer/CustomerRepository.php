<?php

namespace App\Repositories\Customer;

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