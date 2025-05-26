<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->date('slot_date')->nullable(false);
            $table->time('start_time')->nullable(false);
            $table->time('end_time')->nullable(false);
            $table->integer('max_employees')->default(2);
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
