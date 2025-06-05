<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->string('phone',11)->nullable(false);
            $table->string('otp_code',6)->nullable(false);
            $table->enum('purpose',['login', 'register', 'reset_password', 'verify_phone'])->nullable(false);
            $table->tinyInteger('is_used')->default(0);
            $table->integer('attempts')->default(0);
            $table->timestamp('expires_at')->nullable(false);
            $table->timestamps();

            $table->index('otp_code','idx_otp_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
