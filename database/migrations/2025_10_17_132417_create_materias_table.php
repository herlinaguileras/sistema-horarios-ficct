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
      Schema::create('materias', function (Blueprint $table) {
    $table->id();
    $table->string('nombre');
    $table->string('sigla')->unique();
    $table->integer('nivel_semestre'); // (Ej: 1, 2, 3...)
    $table->string('carrera'); // (Ej: 'Sistemas', 'Redes', 'Informatica', 'Robotica')
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materias');
    }
};
