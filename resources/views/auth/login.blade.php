@extends('layouts.emptylayout')

@section('title', 'Login')

@section('content')
 

    <div class="login-box">
        <h4 class="text-center mb-4">Login to your account</h4>

        <form method="POST">
            <div class="form-group">
                <input type="email" id="email" name="email" class="form-control" placeholder="Email" required autofocus>
            </div>

            <div class="form-group position-relative">
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                <span class="position-absolute" style="right: 10px; top: 10px; cursor: pointer;"><i class="bi bi-eye-fill"></i></span>
            </div>

            <div class="form-group d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>
                <a href="#" class="text-login-custom">Forgot Password?</a>
            </div>

            <button onclick="loginAsync()" type="button" class="btn btn-custom btn-block mb-3">Sign in with email</button>
        </form>
        <div id="message" class="text-center mt-3" style="color: green;"></div>
        <div id="error" class="text-center mt-3" style="color: red;"></div>

        <div class="text-center mb-3">— Or login with —</div>
        <div class="d-flex justify-content-between">
            <button class="btn btn-light w-100 mr-2">
                <img src="https://img.icons8.com/color/20/000000/google-logo.png"/> Google
            </button>
            <button class="btn btn-light w-100 ml-2">
                <img src="https://img.icons8.com/ios-filled/20/000000/mac-os.png"/> Apple
            </button>
        </div>

        <div class="text-center mt-3">
            Don’t have an account? <a href={{ Route('register') }} class="text-login-custom font-weight-bold">Get Started</a>
        </div>
    </div>
@endsection
@section('scripts')
   @include('scripts.auth-scripts')
@endsection
