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
       Schema::create('horarios', function (Blueprint $table) {
            $table->id();

            // --- Conexiones (Llaves Foráneas) ---
            $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
            $table->foreignId('aula_id')->constrained('aulas');

            // --- Definición del Horario ---
            $table->tinyInteger('dia_semana'); // 1 = Lunes, 2 = Martes, ..., 7 = Domingo
            $table->time('hora_inicio'); // Ej: "08:00"
            $table->time('hora_fin'); // Ej: "10:00"

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
