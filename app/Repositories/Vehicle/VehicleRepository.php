<?php

namespace App\Repositories\Vehicle;

use App\Models\Vehicle;
use App\Repositories\BaseRepository;

class VehicleRepository extends BaseRepository implements VehicleRepositoryInterface{

    public function __construct(Vehicle $vehicle){
        parent::__construct($vehicle);
    }

    public function filters($filters){

    }

    public function softDelete($id){
        
    }

    public function getByCustomer($customerId){
        return Vehicle::where('customer_id',$customerId)->first();
    }
}