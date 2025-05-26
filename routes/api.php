<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TimeSlotController;
use Illuminate\Support\Facades\Route;

Route::get('/services/getAll',[ServiceController::class,'getAll']);

Route::get('/employees/getAll',[EmployeeController::class,'getAll']);

Route::post('/timeslots/generate-slot',[TimeSlotController::class,'generateTimeSlot']);
Route::post('/timeslots/generate-db',[TimeSlotController::class,'generateTimeSlotDb']);
