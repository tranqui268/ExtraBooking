<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id')->unsigned()->nullable(false);
            $table->string('license_plate',20)->unique()->nullable(false);
            $table->string('brand',50)->nullable(false);
            $table->string('model',50)->nullable(false);
            $table->integer('year_manufactory');
            $table->string('engine_number',50);
            $table->string('chassis_number',50);
            $table->enum('fuel_type',['gasoline', 'diesel', 'electric', 'hybrid']);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();

            $table->index('license_plate','idx_license_plate');
            $table->index('customer_id','idx_customer_vehicle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
