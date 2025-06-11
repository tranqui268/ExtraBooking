<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;

class SmsService{
    protected $vonage;

    public function __construct(){
        $basic = new Basic(env('VONAGE_KEY'),env('VONAGE_SECRET'));
        $this->vonage = new Client($basic);
    }

    public function sendSms($to, $message){
        try {
            
            $response = $this->vonage->sms()->send(
                new SMS($to, env('VONAGE_FROM', 'LaravelApp'), $message)
            );

            $status = $response->current()->getStatus();
            return $status === 0;
        } catch (\Exception $e) {
            Log::error('Vonage SMS Error: ' . $e->getMessage());
            return false;
        }
    }
}