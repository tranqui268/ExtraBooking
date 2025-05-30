<?php

namespace App\Repositories\Service;

use App\Models\Service;
use App\Repositories\BaseRepository;
use Illuminate\Support\Str;

class ServiceRepository extends BaseRepository implements ServiceRepositoryInterface{
    public function __construct(Service $service){
        parent::__construct($service);
    }

    public function filters($filters)
    {
        $query = $this->model->query();

        if (!empty($filters['service_name'])) {
            $query->where('service_name','like', '%'. $filters['service_name'] .'%');
        }

        return $query->orderBy('created_at','desc')->get();
    }

    public function generateId($serviceName){
        $firstChar = Str::ascii($serviceName)[0];
        $firstChar = strtoupper($firstChar); 


        $count = Service::where('id', 'LIKE', $firstChar . '%')->count();

        $number = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        return $firstChar . $number;
    }

    public function softDelete($id)
    {
        
    }

}