<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('repair_order_id')->unsigned()->nullable(false);
            $table->decimal('rating',3,1)->nullable();
            $table->text('comment');
            $table->text('response');
            $table->tinyInteger('is_approved')->default(0);
            $table->timestamps();

            $table->foreign('repair_order_id')
                  ->references('id')
                  ->on('repair_orders')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
