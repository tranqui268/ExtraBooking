@extends('layouts.emptylayout')

@section('title', 'Register')

@section('content')
 

    <div class="login-box">
        <h4 class="text-center mb-4">Create your ID</h4>

        <form id="register-form" method="POST">           
            <div class="form-group">
                <input id="email" type="email" name="email" class="form-control" placeholder="Email" required autofocus>
                <small class="text-danger" id="emailError"></small>
            </div>

            <div class="form-group">
                <input id="name" type="text" name="name" class="form-control" placeholder="Name" required>
                <small class="text-danger" id="nameError"></small>
            </div>

            <div class="form-group">
                <input id="phone" type="tel" name="phone" class="form-control" placeholder="Phone number" required>
                <small class="text-danger" id="phoneError"></small>
            </div>

            <div class="form-group position-relative">
                <input id="password" type="password" name="password" class="form-control" placeholder="Password" required>
                <small class="text-danger" id="passwordError"></small>
            </div>

            <div class="form-group position-relative">
                <input id="confirmPassword" type="password" name="confirmPassword" class="form-control" placeholder="Password Confirm" required>
                <small class="text-danger" id="passwordConfirmError"></small>
            </div>
      
            <button onclick="registerAsync()" type="button" class="btn btn-custom btn-block mb-3">Sign up with email</button>
        </form>     

        <div class="text-center mt-3">
            Already have an account? <a href={{ Route('login') }} class="text-login-custom font-weight-bold">Login Now</a>
        </div>
    </div>
@endsection
@section('scripts')
    @include('scripts.auth-scripts')
@endsection
