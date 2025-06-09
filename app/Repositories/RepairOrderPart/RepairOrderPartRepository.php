<?php

namespace App\Repositories\RepairOrderPart;

use App\Models\RepairOrderPart;
use App\Repositories\BaseRepository;

class RepairOrderPartRepository extends BaseRepository implements RepairOrderPartRepositoryInterface{
    public function __construct(RepairOrderPart $repairOrderPart){
        parent::__construct($repairOrderPart);
    }

    public function filters($filters){

    }

    public function softDelete($id){
        
    }
}