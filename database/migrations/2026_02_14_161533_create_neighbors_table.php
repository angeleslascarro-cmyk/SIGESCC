<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('neighbors', function (Blueprint $table) {
    $table->id();
    $table->string('full_name');
    $table->string('cedula')->unique();
    $table->string('phone')->nullable();
    $table->string('address')->nullable();
    $table->decimal('credit_limit', 12, 2)->default(0);
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neighbors');
    }
};
