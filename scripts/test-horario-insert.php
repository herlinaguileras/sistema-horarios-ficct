<?php

/**
 * Script para probar la inserción de horarios
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Horario;
use App\Models\Grupo;
use App\Models\Aula;

echo "============================================\n";
echo "  TEST: Inserción de Horario\n";
echo "============================================\n\n";

// Buscar un grupo y un aula
$grupo = Grupo::first();
$aula = Aula::first();

if (!$grupo || !$aula) {
    echo "❌ ERROR: No hay grupos o aulas en la BD\n";
    echo "   Crea primero un grupo y un aula\n";
    exit(1);
}

echo "✓ Grupo encontrado: ID {$grupo->id}\n";
echo "✓ Aula encontrada: ID {$aula->id}\n\n";

// Intentar crear un horario de prueba
try {
    $horario = Horario::create([
        'grupo_id' => $grupo->id,
        'aula_id' => $aula->id,
        'dia_semana' => 2, // Martes (ahora es número)
        'hora_inicio' => '18:15',
        'hora_fin' => '20:30'
    ]);

    echo "✅ HORARIO CREADO EXITOSAMENTE\n";
    echo "   ID: {$horario->id}\n";
    echo "   Día: {$horario->dia_semana} (2 = Martes)\n";
    echo "   Hora: {$horario->hora_inicio} - {$horario->hora_fin}\n\n";

    // Eliminar el registro de prueba
    $horario->delete();
    echo "✓ Registro de prueba eliminado\n\n";

    echo "╔══════════════════════════════════════════╗\n";
    echo "║  ✅ TEST PASADO - TODO FUNCIONA         ║\n";
    echo "╚══════════════════════════════════════════╝\n";

} catch (Exception $e) {
    echo "❌ ERROR AL CREAR HORARIO:\n";
    echo "   {$e->getMessage()}\n\n";
    echo "╔══════════════════════════════════════════╗\n";
    echo "║  ❌ TEST FALLIDO                         ║\n";
    echo "╚══════════════════════════════════════════╝\n";
}
