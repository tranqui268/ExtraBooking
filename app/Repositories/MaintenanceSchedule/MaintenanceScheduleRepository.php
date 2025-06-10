<?php

namespace App\Repositories\MaintenanceSchedule;

use App\Models\MaintenanceSchedule;
use App\Repositories\BaseRepository;

class MaintenanceScheduleRepository extends BaseRepository implements MaintenanceScheduleRepositoryInterface{

    public function __construct(MaintenanceSchedule $maintenanceSchedule){
        parent::__construct($maintenanceSchedule);
    }

    public function filters($filters){

    }

    public function softDelete($id){
        
    }
}