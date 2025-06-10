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

    public function vehicleLookup($data){
        $input = $data['input'];
        $vehicle = Vehicle::where('license_plate',$input)
            ->orWhereHas('customer', function($query) use ($input){
                $query->where('phone',$input);
            })
            ->with('customer')
            ->first();

        return [
            'license_plate' => $vehicle->license_plate,
            'brand' => $vehicle->brand,
            'model' => $vehicle->model,
            'year_manufactory' => $vehicle->year_manufactory,
            'engine_number' => $vehicle->engine_number,
            'chassis_number' => $vehicle->chassis_number,
            'fuel_type' => $vehicle->fuel_type,
            'customer_name' => $vehicle->customer->name ?? 'N/A',
            'customer_phone' => $vehicle->customer->phone ?? 'N/A',
            'repair_count' => $vehicle->maintenanceSchedules->count(),
        ];
    }
}