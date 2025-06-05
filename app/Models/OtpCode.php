<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'phone',
        'purpose',
        'is_used',
        'attempts',
        'expires_at'
    ];

    protected $hidden = [
        'otp_code'
    ];
}
