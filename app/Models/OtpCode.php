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
        'otp_code',
        'purpose',
        'is_used',
        'attempts',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    public function isExpired(){
        return $this->expires_at < now();
    }

    public function isValid(){
        return !$this->is_used && !$this->isExpired() && $this->attempts < 3;
    }

    public function incrementAttempts()
    {
        $this->increment('attempts');
    }

    public function markAsUsed()
    {
        $this->update([
            'is_used' => true,
            'updated_at' => now()
        ]);
    }
}
