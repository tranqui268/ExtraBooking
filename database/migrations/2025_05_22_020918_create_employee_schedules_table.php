<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id',5)->nullable(false);
            $table->bigInteger('slot_id')->unsigned()->nullable(false);
            $table->bigInteger('appointment_id')->unsigned()->nullable();
            $table->enum('status',['available', 'booked', 'unavailable'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_schedules');
    }
};
