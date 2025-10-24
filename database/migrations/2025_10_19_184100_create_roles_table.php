<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            // 'name' should be unique (e.g., 'admin', 'docente')
            $table->string('name')->unique();
            $table->string('description')->nullable(); // Optional description
            $table->timestamps();
        });
    }

    // Add the down() method
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
