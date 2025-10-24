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
        Schema::create('titulos', function (Blueprint $table) {
        $table->id();

        // Esta es la conexión: "Este título le pertenece a un docente"
        $table->foreignId('docente_id')
            ->constrained('docentes')
              ->onDelete('cascade'); // Si se borra el docente, se borran sus títulos

        $table->string('nombre'); // El título (Ej: "Ingeniero de Sistemas", "Maestría en Redes")

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titulos');
    }
};
