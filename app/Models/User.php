<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $primaryKey = 'id';
    public $incrementing = true;
    
    protected $fillable = [
        'email',
        'password',
        'role',
        'is_active',
        'is_delete'
    ];

   
    protected $hidden = [
        'password',
    ];

   
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class
        ];
    }

    public function customer(){
        return $this->hasOne(Customer::class);
    }

    public function employee(){
        return $this->hasOne(Employee::class);
    }
}
