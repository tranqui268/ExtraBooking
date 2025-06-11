<?php

namespace App\Services;

use App\Models\OtpCode;
use App\Repositories\OtpCode\OtpCodeRepositoryInterface;

class OtpService{
    protected $smsService;
    protected $otpRepo;

    public function __construct(SmsService $smsService,OtpCodeRepositoryInterface $otpRepo){
        $this->smsService = $smsService;
        $this->otpRepo = $otpRepo;
    }

    public function generateOtp($phone, $purpose = 'login'){
        $this->otpRepo->clearOtpNotUse($phone, $purpose);

        $phoneNumber = '+84' . substr($phone,1);
        $otpCode = \rand(100000,999999);

        $otp = $this->otpRepo->create([
            'phone' => $phone,
            'otp_code' => $otpCode,
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(5)
        ]);

        $this->smsService->sendSms($phoneNumber,"Mã xác nhận của bạn là: $otpCode . Mã xác nhận có thời hạn trong 5 phút");

        return $otp;
    }

    public function verifyOtp($phone, $otpCode, $purpose = 'login'){
        $otp = $this->otpRepo->getOtp($phone,$otpCode,$purpose);

        if (!$otp) {
            return ['success'=>false, 'message'=>'Mã OTP không hợp lệ'];
        }

        if ($otp->isExpired()) {
            return ['success'=>false, 'message'=>'Mã OTP đã hết hạn'];
        }

        if ($otp->attempts >= 3) {
            return ['success'=>false, 'message'=>'Bạn đã nhập sai quá nhiều lần'];
        }

        $otp->markAsUsed();

        return ['success' => true, 'message' => 'Xác thực thành công'];
    }

    public function cleanupExpiredOtps(){
        OtpCode::where('expires_at','<', now())->delete();
    }
}