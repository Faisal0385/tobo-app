<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string(column: 'name')->nullable();
            $table->string(column: 'email')->unique();
            $table->string(column: 'password');
            $table->string(column: 'phone');
            $table->string(column: 'email_verify')->nullable();
            $table->string(column: 'otp')->nullable();
            $table->string(column: 'image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
