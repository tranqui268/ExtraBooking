<?php

namespace App\Repositories\Service;

use App\Models\Service;
use App\Repositories\BaseRepository;

class ServiceRepository extends BaseRepository implements ServiceRepositoryInterface{
    public function __construct(Service $service){
        parent::__construct($service);
    }

    public function filters($filters)
    {
        
    }

    public function softDelete($id)
    {
        
    }

}