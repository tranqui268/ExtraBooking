@extends('layouts.emptylayout')

@section('title', 'Login')

@section('content')
 

    <div id="phone-step" class="login-box">
        <h4 class="text-center mb-4">Login to your account</h4>

        <form method="POST">
            <div class="form-group">
                <input id="phone" type="tel" name="phone" class="form-control" placeholder="Phone number" required>
                <small class="text-danger" id="phoneError"></small>
            </div>
            <button onclick="sendOtp()" type="button" class="btn btn-custom btn-block mb-3">Send otp</button>
        </form>
        <div class="text-center mt-3">
            Login with account? <a href={{ Route('login') }} class="text-login-custom font-weight-bold">Login Now</a>
        </div>     
    </div>
    <div id="otp-step" class="hidden">
        <form method="POST" id="otpForm">
            <div class="otp-container">
                @for ($i = 1; $i <= 6; $i++)
                    <input type="text" class="otp-input" name="otp[]" maxlength="1" data-index="{{ $i }}" required>
                @endfor
            </div>
            <button type="button" class="btn btn-custom btn-block mb-3" onclick="verifyOtp()">Verify otp</button>
            <div class="text-center mt-3">
                Login with account? <a href={{ Route('login') }} class="text-login-custom font-weight-bold">Login Now</a>
            </div>
        </form>

        <div class="resend-timer">
            <span id="timer-text">Gửi lại mã sau: <span id="countdown">60</span>s</span>
            <button id="resend-btn" class="resend-btn" onclick="resendOtp()">Gửi lại mã OTP</button>
        </div>
    </div>
@endsection
@section('scripts')
   @include('scripts.auth-scripts')
@endsection
