<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->string('id',4)->primary();
            $table->string('service_name',50)->nullable(false);
            $table->integer('duration')->default(20);
            $table->decimal('base_price',8,2)->nullable(false);
            $table->integer('maintenance_interval');
            $table->string('description',255)->nullable();
            $table->tinyInteger('is_delete')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
