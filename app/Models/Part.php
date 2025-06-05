<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'part_code',
        'name',
        'brand',
        'description',
        'stock_quantity',
        'is_active'
    ];

    public function repairOrderPart (){
        return $this->hasMany(RepairOrderPart::class,'part_id');
    }
}
