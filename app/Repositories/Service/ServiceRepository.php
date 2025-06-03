<?php

namespace App\Repositories\Service;

use App\Helpers\CacheHelper;
use App\Models\Service;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
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

    public function update($id, array $data){
        try {
            $updated = Service::where('id',$id)->update($data);
            if ($updated) {
                CacheHelper::clearCache('service:all');
            }
            return $updated;
        } catch (\Exception $e) {
            Log::error('Error updating service', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function create(array $data){
        try {
            $service = Service::create($data);
            CacheHelper::clearCache('service:all');
            return $service;
        } catch (\Exception $e) {
            Log::error('Error creating service', ['error' => $e->getMessage()]);
            throw $e;
        }
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