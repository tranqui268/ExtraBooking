<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable=['id','user_id','name','email','phone','address','store_point','is_delete'];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function appointment(){
        return $this->hasMany(Appointment::class,'customer_id');
    }

    public function vehicle(){
        return $this->hasMany(Vehicle::class,'customer_id');
    }
}
