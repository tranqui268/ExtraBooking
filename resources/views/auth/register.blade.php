@extends('layouts.emptylayout')

@section('title', 'Register')

@section('content')
 

    <div class="login-box">
        <h4 class="text-center mb-4">Create your ID</h4>

        <form method="POST">
            @csrf
            <div class="form-group d-flex">
                <input type="string" name="name" class="form-control" placeholder="Name" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Email" required autofocus>
            </div>

            <div class="form-group position-relative">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
                <span class="position-absolute" style="right: 10px; top: 10px; cursor: pointer;"><i class="bi bi-eye-fill"></i></span>
            </div>

            <div class="form-group position-relative">
                <input type="password" name="confirmPassword" class="form-control" placeholder="Password Confirm" required>
                <span class="position-absolute" style="right: 10px; top: 10px; cursor: pointer;"><i class="bi bi-eye-fill"></i></span>
            </div>
      

            <button type="submit" class="btn btn-custom btn-block mb-3">Sign up with email</button>
        </form>

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
            Already have an account? <a href="#" class="text-login-custom font-weight-bold">Login Now</a>
        </div>
    </div>
@endsection
