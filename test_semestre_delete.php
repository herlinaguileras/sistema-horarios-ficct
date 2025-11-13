<?php

/**
 * Script de prueba para verificar eliminación de semestres
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Semestre;

echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║  TEST: ELIMINACIÓN DE SEMESTRES                         ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

$semestres = Semestre::with('grupos')->get();

echo "📋 ANÁLISIS DE SEMESTRES:\n";
echo str_repeat('─', 60) . "\n\n";

foreach ($semestres as $semestre) {
    $gruposCount = $semestre->grupos()->count();
    $esActivo = $semestre->isActivo();
    $puedeEliminar = !$esActivo && $gruposCount === 0;

    echo "🔹 {$semestre->nombre} (ID: {$semestre->id})\n";
    echo "   Estado: {$semestre->estado}\n";
    echo "   Es activo: " . ($esActivo ? '✅ SÍ' : '❌ NO') . "\n";
    echo "   Grupos: {$gruposCount}\n";
    echo "   Puede eliminar: " . ($puedeEliminar ? '✅ SÍ' : '❌ NO') . "\n";

    if (!$puedeEliminar) {
        echo "   Razón: ";
        if ($esActivo) {
            echo "Es el semestre activo\n";
        } elseif ($gruposCount > 0) {
            echo "Tiene {$gruposCount} grupo(s) asociado(s)\n";
        }
    }
    echo "\n";
}

echo "\n" . str_repeat('═', 60) . "\n";
echo "✅ Análisis completado\n";
