<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TimeSlotController;
use App\Http\Controllers\WorkingHourController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);

Route::get('/services/getAll',[ServiceController::class,'getAll'])->middleware('auth:sanctum');

Route::get('/employees/getAll',[EmployeeController::class,'getAll']);

Route::post('/timeslots/generate-slot',[TimeSlotController::class,'generateTimeSlot']);
Route::post('/timeslots/generate-db',[TimeSlotController::class,'generateTimeSlotDb']);

Route::prefix('appointments')->group(function () {
    Route::get('/available-slots', [AppointmentController::class, 'getAvailableTimeSlots']);
    Route::post('/book', [AppointmentController::class, 'bookAppointment']);
    Route::put('/{appointmentId}/cancel', [AppointmentController::class, 'cancelAppointment']);
    Route::get('/byCustomer/{customerId}',[AppointmentController::class, 'getAppointmentsByCustomer']);
    Route::get('/bookings',[AppointmentController::class,'getAppointmentsUser']);
    Route::get('/{appointmentId}',[AppointmentController::class,'getById']);
    Route::get('/',[AppointmentController::class,'getWithFilters']);
})->middleware(['auth:sanctum']);



Route::prefix('customers')->group(function(){
    Route::post('/create',[CustomerController::class,'create']);
    Route::get('/',[CustomerController::class,'getAllCustomer']);
});

Route::prefix('services')->group(function(){
    Route::get('/',[ServiceController::class,'getWithFilters']);
    Route::post('/',[ServiceController::class,'create']);
    Route::put('/{id}',[ServiceController::class,'update']);
});

Route::prefix('workinghours')->group(function(){
    Route::get('/byDate',[WorkingHourController::class, 'getWorkingHourByDate']);
});

