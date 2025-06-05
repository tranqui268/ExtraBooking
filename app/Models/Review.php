<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'repair_order_id',
        'rating',
        'comment',
        'response',
        'is_approved',
    ];

    public function repairOrder(){
        return $this->belongsTo(RepairOrder::class,'repair_order_id');
    }
}
