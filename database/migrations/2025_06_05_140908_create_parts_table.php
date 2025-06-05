<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->string('part_code',30)->nullable(false)->unique();
            $table->string('name',100)->nullable(false);
            $table->string('brand',50);
            $table->text('description');
            $table->decimal('unit_price',10,2)->nullable(false);
            $table->integer('stock_quantity')->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};
