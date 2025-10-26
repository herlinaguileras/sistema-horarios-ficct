<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to rename columns in PostgreSQL
        // This avoids needing the doctrine/dbal dependency
        DB::statement('ALTER TABLE asistencias RENAME COLUMN hsora_registro TO hora_registro');
        DB::statement('ALTER TABLE asistencias RENAME COLUMN etado TO estado');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the column names
        DB::statement('ALTER TABLE asistencias RENAME COLUMN hora_registro TO hsora_registro');
        DB::statement('ALTER TABLE asistencias RENAME COLUMN estado TO etado');
    }
};
