<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairOrderPart extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'repair_order_id',
        'part_id',
        'quantity',
        'notes'
    ];

    public function repairOrder(){
        return $this->belongsTo(RepairOrder::class,'repair_order_id');
    }

    public function part(){
        return $this->belongsTo(Part::class,'part_id');
    }
}
