<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')
                  ->onDelete('cascade');

            $table->foreign('employee_id')
                  ->references('id')
                  ->on('employees')
                  ->onDelete('restrict');

            $table->foreign('service_id')
                  ->references('id')
                  ->on('services')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['customer_id']);
        });
    }
};
