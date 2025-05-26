<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.app');
});

Route::get('/services',[ServiceController::class,'index']);
Route::get('/employees',[EmployeeController::class,'index']);
Route::get("/bookings",[BookingController::class,'index']);
