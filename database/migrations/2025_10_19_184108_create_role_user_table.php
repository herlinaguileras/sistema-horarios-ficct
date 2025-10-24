<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::create('role_user', function (Blueprint $table) {
            // Foreign key for the User
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Foreign key for the Role
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            // Make the combination unique to prevent duplicates
            $table->primary(['user_id', 'role_id']);
            // No timestamps needed for a simple pivot table
        });
    }

    // Add the down() method
    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
