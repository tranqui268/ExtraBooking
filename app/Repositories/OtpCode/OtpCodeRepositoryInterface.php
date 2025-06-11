<?php

namespace App\Repositories\OtpCode;

use App\Repositories\RepositoryInterface;

interface OtpCodeRepositoryInterface extends RepositoryInterface{
    public function updateStatus($code);

    public function clearOtpNotUse($phone, $purpose);

    public function getOtp($phone,$otp,$purpose);
}