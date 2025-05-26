<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_schedules', function (Blueprint $table) {
            $table->foreign('employee_id')
                  ->references('id')
                  ->on('employees')
                  ->onDelete('cascade');

            $table->foreign('slot_id')
                  ->references('id')
                  ->on('time_slots')
                  ->onDelete('cascade');

            $table->foreign('appointment_id')
                  ->references('id')
                  ->on('appointments')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('employee_schedules', function (Blueprint $table) {
            $table->dropForeign(['appointment_id']);
            $table->dropForeign(['slot_id']);
            $table->dropForeign(['employee_id']);
        });
    }
};
