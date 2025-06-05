<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_order_parts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('repair_order_id')->unsigned()->nullable(false);
            $table->bigInteger('part_id')->unsigned()->nullable(false);
            $table->integer('quantity')->nullable(false);
            $table->text('notes');
            $table->timestamps();

            $table->foreign('repair_order_id')
                  ->references('id')
                  ->on('repair_orders')
                  ->onDelete('restrict');
            
            $table->foreign('part_id')
                  ->references('id')
                  ->on('parts')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_order_parts');
    }
};
