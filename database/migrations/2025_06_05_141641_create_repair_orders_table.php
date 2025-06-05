<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('repair_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('appointment_id')->unsigned()->nullable(false);
            $table->text('description');
            $table->text('diagnosis');
            $table->text('work_performed');
            $table->text('technician_notes');
            $table->decimal('labor_cost',10,2)->default(0);
            $table->decimal('parts_cost',10,2)->default(0);
            $table->decimal('total_cost',10,2)->default(0);

            $table->timestamps();
            $table->foreign('appointment_id')
                  ->references('id')
                  ->on('appointments')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_orders');
    }
};
