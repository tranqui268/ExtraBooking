<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('maintenance_schedule_id')->unsigned()->nullable(false);
            $table->enum('type',['maintenance_reminder', 'appointment_confirmation', 'service_completed', 'review_request']);
            $table->string('title',200)->nullable(false);
            $table->text('message')->nullable(false);
            $table->enum('send_via',['email', 'sms', 'both'])->default('both');
            $table->timestamp('scheduled_time')->nullable(false);
            $table->timestamp('send_time');
            $table->enum('status',['pending', 'sent', 'failed', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->foreign('maintenance_schedule_id')
                  ->references('id')
                  ->on('maintenance_schedules')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
