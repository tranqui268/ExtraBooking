<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id')->unsigned()->nullable(false);
            $table->string('employee_id',5)->nullable(false);
            $table->string('service_id',4)->nullable(false);
            $table->date('appointment_date')->nullable(false);
            $table->time('start_time')->nullable(false);
            $table->time('end_time')->nullable(false);
            $table->enum('status',['pending','confirmed','cancelled','completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
