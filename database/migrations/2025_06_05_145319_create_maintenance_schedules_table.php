<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vehicle_id')->unsigned()->nullable(false);
            $table->string('service_id',4)->nullable(false);
            $table->date('last_maintenance_date');
            $table->date('next_maintenance_date')->nullable(false);
            $table->integer('maintenance_interval')->nullable(false);
            $table->integer('mileage_interval');
            $table->integer('current_mileage');
            $table->enum('status',['pending', 'notified', 'scheduled', 'completed', 'overdue'])->default('pending');
            $table->enum('priority',['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->text('notes');
            $table->timestamps();

            $table->foreign('vehicle_id')
                  ->references('id')
                  ->on('vehicles')
                  ->onDelete('restrict');

            $table->foreign('service_id')
                  ->references('id')
                  ->on('services')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};
