<?php

namespace App\Repositories\Part;

use App\Models\Part;
use App\Repositories\BaseRepository;

class PartRepository extends BaseRepository implements PartRepositoryInterface{

    public function __construct(Part $part){
        parent::__construct($part);
    }

    public function filters($filters){

    }

    public function softDelete($id){
        
    }

    public function updatePartsStock(array $partIds, array $quantities){
        foreach ($partIds as $index => $partId){
            $quanity = $quantities[$index];

            $part = Part::find($partId);
            if (!$part) {
                return false;
            }

            $part->stock_quantity = max(0, $part->stock_quantity - $quanity);
            if (!$part->save()) {
                return false;
            }
        }
        return true;
    }
}