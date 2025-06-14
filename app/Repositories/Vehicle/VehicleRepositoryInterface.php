<?php

namespace App\Repositories\Vehicle;

use App\Repositories\RepositoryInterface;

interface VehicleRepositoryInterface extends RepositoryInterface{
    public function getByCustomer($customerId);
    public function vehicleLookup($data);
}