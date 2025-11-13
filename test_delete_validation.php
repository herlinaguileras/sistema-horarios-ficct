<?php

/**
 * Script de prueba para validar la lógica de eliminación de semestres
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Semestre;

echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║  VALIDACIÓN DE LÓGICA DE ELIMINACIÓN                    ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

$semestres = Semestre::with('grupos')->get();

foreach ($semestres as $semestre) {
    $gruposCount = $semestre->grupos()->count();
    $esActivo = $semestre->isActivo();

    echo "📋 Semestre: {$semestre->nombre}\n";
    echo str_repeat('─', 60) . "\n";

    // Validación 1: Es activo?
    echo "✓ Validación 1 - ¿Es activo?: ";
    if ($esActivo) {
        echo "❌ SÍ (BLOQUEAR)\n";
        echo "  → Mensaje: No se puede eliminar el semestre activo.\n";
        echo "  → Acción: Cambiar a 'Planificación' o 'Terminado'\n";
    } else {
        echo "✅ NO (PERMITIR)\n";
    }

    // Validación 2: Tiene grupos?
    echo "✓ Validación 2 - ¿Tiene grupos?: ";
    if ($gruposCount > 0) {
        echo "❌ SÍ - {$gruposCount} grupo(s) (BLOQUEAR)\n";
        echo "  → Mensaje: Tiene grupos asociados\n";
        echo "  → Acción: Eliminar grupos primero\n";
        echo "  → Grupos:\n";
        foreach ($semestre->grupos->take(3) as $grupo) {
            echo "    • {$grupo->materia->nombre} - Grupo {$grupo->nombre}\n";
        }
        if ($gruposCount > 3) {
            echo "    • ... y " . ($gruposCount - 3) . " más\n";
        }
    } else {
        echo "✅ NO (PERMITIR)\n";
    }

    // Validación 3: Estado válido?
    echo "✓ Validación 3 - ¿Estado válido?: ";
    $estadosValidos = [Semestre::ESTADO_PLANIFICACION, Semestre::ESTADO_TERMINADO];
    if (in_array($semestre->estado, $estadosValidos)) {
        echo "✅ SÍ - '{$semestre->estado}' (PERMITIR)\n";
    } else {
        echo "❌ NO - '{$semestre->estado}' (BLOQUEAR)\n";
        echo "  → Solo se permite: Planificación, Terminado\n";
    }

    // Resultado final
    echo "\n🎯 RESULTADO FINAL: ";
    $puedeEliminar = !$esActivo && $gruposCount === 0 && in_array($semestre->estado, $estadosValidos);

    if ($puedeEliminar) {
        echo "✅ SE PUEDE ELIMINAR\n";
        echo "  → Botón: HABILITADO (rojo)\n";
        echo "  → Backend: Eliminación PERMITIDA\n";
    } else {
        echo "❌ NO SE PUEDE ELIMINAR\n";
        echo "  → Botón: DESHABILITADO (gris)\n";
        echo "  → Backend: Eliminación BLOQUEADA\n";
    }

    echo "\n" . str_repeat('═', 60) . "\n\n";
}

echo "✅ Validación completada\n\n";

// Resumen
echo "📊 RESUMEN:\n";
echo str_repeat('─', 60) . "\n";
$eliminables = $semestres->filter(function($s) {
    return !$s->isActivo() &&
           $s->grupos()->count() === 0 &&
           in_array($s->estado, [Semestre::ESTADO_PLANIFICACION, Semestre::ESTADO_TERMINADO]);
})->count();

echo "Total de semestres: {$semestres->count()}\n";
echo "Eliminables: {$eliminables}\n";
echo "No eliminables: " . ($semestres->count() - $eliminables) . "\n";
