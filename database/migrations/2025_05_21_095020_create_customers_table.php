<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('name', 50)->nullable(false);
            $table->string('email')->unique();
            $table->string('phone',11)->nullable(false);
            $table->string('address')->nullable();
            $table->integer('store_point')->default(0);
            $table->tinyInteger('is_delete')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
