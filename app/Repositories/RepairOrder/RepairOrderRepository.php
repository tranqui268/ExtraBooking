<?php

namespace   App\Repositories\RepairOrder;

use App\Models\RepairOrder;
use App\Repositories\BaseRepository;

class RepairOrderRepository extends BaseRepository implements RepairOrderRepositoryInterface{

    public function __construct(RepairOrder $repairOrder){
        parent::__construct($repairOrder);
    }

    public function filters($filters){

    }

    public function softDelete($id){
        
    }
}