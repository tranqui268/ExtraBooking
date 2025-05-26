<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id','name','experience','rating','is_active','is_delete'];

    public function schedules(){
        return $this->hasMany(EmployeeSchedule::class,'employee_id','id');
    }
}
