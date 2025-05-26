<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('working_hours', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('day_of_week')->unsigned()->unique();
            $table->time('start_time')->nullable(false);
            $table->time('end_time')->nullable(false);
            $table->tinyInteger('is_working_day')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('working_hours');
    }
};
