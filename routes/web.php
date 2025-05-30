<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/',[AuthController::class,'index']);
Route::get('/register',[AuthController::class,'showRegister'])->name('register');
Route::get('/login',[AuthController::class,'index'])->name('login');

Route::get('/services',[ServiceController::class,'index']);
Route::get('/employees',[EmployeeController::class,'index']);
Route::get("/bookings",[BookingController::class,'index']);
Route::get('/appointments',[AppointmentController::class,'showAppointmentUser']);

Route::get('/admin', function(){
    return view('layouts.admin');
});

Route::get('/employee', function(){
    return view('employee.dashboard');
});


Route::prefix('/admin')->group(function(){
    Route::get('/customers', [CustomerController::class,'index']);
    Route::get('/services',[ServiceController::class,'showDashboard'] );
});



