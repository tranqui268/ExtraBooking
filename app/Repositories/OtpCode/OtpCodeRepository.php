<?php

namespace App\Repositories\OtpCode;

use App\Models\OtpCode;
use App\Repositories\BaseRepository;

class OtpCodeRepository extends BaseRepository implements OtpCodeRepositoryInterface{

    public function __construct(OtpCode $otpCode){
        parent::__construct($otpCode);
    }

    public function filters($filters){

    }

    public function softDelete($id){

    }

    public function updateStatus($code){
        return OtpCode::where('otp_code',$code)
                    ->where('is_used',0)
                    ->update([
                        'is_used' => 1
                    ]);
    }

    public function clearOtpNotUse($phone, $purpose){
        return OtpCode::where('phone',$phone)
                    ->where('purpose',$purpose)
                    ->where('is_used',0)
                    ->delete();
    }

    public function getOtp($phone, $otp, $purpose){
        return OtpCode::where('phone',$phone)
                     ->where('otp_code',$otp)
                     ->where('is_used',0)
                     ->first();
    }
}