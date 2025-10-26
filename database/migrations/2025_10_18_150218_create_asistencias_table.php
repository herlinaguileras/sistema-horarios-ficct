<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('asistencias', function (Blueprint $table) {
        $table->id();

        // --- Conexiones ---
        // A qué horario específico pertenece esta asistencia
        $table->foreignId('horario_id')->constrained('horarios')->onDelete('cascade');
        // Qué docente marcó (Aunque ya está en el grupo, es útil para reportes)
        $table->foreignId('docente_id')->constrained('docentes');

        // --- Datos del Registro ---
        $table->date('fecha'); // El día específico de la clase
        $table->time('hora_registro'); // Hora exacta en que marcó
        $table->string('estado')->default('Presente'); // Ej: 'Presente', 'Ausente', 'Licencia'
        $table->string('metodo_registro')->nullable(); // Ej: 'QR', 'Manual', 'Formulario'

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
