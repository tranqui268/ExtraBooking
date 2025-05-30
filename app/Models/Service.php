<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'service_name','duration', 'description', 'base_price', 'duration_minutes', 'is_delete'];

    public function appointments(){
        return $this->hasMany(Appointment::class,'service_id');
    }
}
