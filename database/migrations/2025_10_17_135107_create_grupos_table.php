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
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();

            // --- Conexiones (Llaves Foráneas) ---
            $table->foreignId('semestre_id')->constrained('semestres');
            $table->foreignId('materia_id')->constrained('materias');
            $table->foreignId('docente_id')->constrained('docentes');

            // --- Información del Grupo ---
            $table->string('nombre'); // Ej: "SA", "SB", "SC"

            $table->timestamps();
        });
    }



  
    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
