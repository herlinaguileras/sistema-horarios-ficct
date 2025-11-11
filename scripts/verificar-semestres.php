<?php

/**
 * Script para verificar semestres y condiciones de eliminación
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Semestre;

echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║  VERIFICACIÓN DE SEMESTRES                               ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n\n";

// 1. Obtener todos los semestres
$semestres = Semestre::with('grupos.materia')->orderBy('fecha_inicio', 'desc')->get();

if ($semestres->isEmpty()) {
    echo "❌ No hay semestres registrados en el sistema.\n";
    echo "   Crea al menos un semestre desde: http://127.0.0.1:8000/semestres/create\n\n";
    exit(0);
}

echo "📊 RESUMEN GENERAL:\n";
echo "─────────────────────────────────────────────────────────────\n";
echo "Total de semestres: {$semestres->count()}\n\n";

// 2. Análisis por estado
$porEstado = [
    'Activo' => 0,
    'Planificación' => 0,
    'Terminado' => 0
];

foreach ($semestres as $semestre) {
    $porEstado[$semestre->estado]++;
}

echo "Distribución por estado:\n";
echo "  • Activos: {$porEstado['Activo']}\n";
echo "  • En Planificación: {$porEstado['Planificación']}\n";
echo "  • Terminados: {$porEstado['Terminado']}\n\n";

echo "═══════════════════════════════════════════════════════════\n\n";

// 3. Detalles de cada semestre
echo "📅 DETALLE DE SEMESTRES:\n";
echo "─────────────────────────────────────────────────────────────\n\n";

$eliminables = 0;
$noEliminables = 0;

foreach ($semestres as $semestre) {
    $gruposCount = $semestre->grupos->count();
    $esActivo = $semestre->isActivo();
    $puedeEliminar = !$esActivo && $gruposCount === 0;
    
    // Icono según estado
    $icono = $esActivo ? '🟢' : ($semestre->estado === 'Planificación' ? '🔵' : '⚫');
    
    echo "{$icono} {$semestre->nombre}\n";
    echo "   ID: {$semestre->id}\n";
    echo "   Estado: {$semestre->estado}\n";
    echo "   Período: {$semestre->fecha_inicio->format('d/m/Y')} - {$semestre->fecha_fin->format('d/m/Y')}\n";
    echo "   Grupos: {$gruposCount}\n";
    
    if ($gruposCount > 0) {
        echo "   Grupos asignados:\n";
        foreach ($semestre->grupos as $grupo) {
            echo "     • {$grupo->materia->nombre} - Grupo {$grupo->nombre}\n";
        }
    }
    
    echo "\n   🗑️ ¿Se puede eliminar?: ";
    
    if ($puedeEliminar) {
        echo "✅ SÍ\n";
        echo "   Acción: Puedes eliminar desde http://127.0.0.1:8000/semestres\n";
        $eliminables++;
    } else {
        echo "❌ NO\n";
        echo "   Razón: ";
        
        if ($esActivo) {
            echo "Es el semestre ACTIVO\n";
            echo "   Solución: Cambia su estado a 'Planificación' o 'Terminado' primero\n";
        } elseif ($gruposCount > 0) {
            echo "Tiene {$gruposCount} grupo(s) asociado(s)\n";
            echo "   Solución: Elimina los grupos o reasígnalos a otro semestre\n";
        }
        
        $noEliminables++;
    }
    
    echo "\n" . str_repeat("─", 60) . "\n\n";
}

// 4. Resumen de eliminabilidad
echo "═══════════════════════════════════════════════════════════\n\n";
echo "📊 RESUMEN DE ELIMINABILIDAD:\n";
echo "─────────────────────────────────────────────────────────────\n";
echo "✅ Semestres que SE PUEDEN eliminar: {$eliminables}\n";
echo "❌ Semestres que NO se pueden eliminar: {$noEliminables}\n\n";

// 5. Semestre activo
$semestreActivo = $semestres->firstWhere('estado', 'Activo');

if ($semestreActivo) {
    echo "🟢 SEMESTRE ACTIVO ACTUAL:\n";
    echo "─────────────────────────────────────────────────────────────\n";
    echo "Nombre: {$semestreActivo->nombre}\n";
    echo "Período: {$semestreActivo->fecha_inicio->format('d/m/Y')} - {$semestreActivo->fecha_fin->format('d/m/Y')}\n";
    echo "Grupos: {$semestreActivo->grupos->count()}\n";
    echo "Estado: Este semestre se muestra en Dashboard y reportes\n\n";
} else {
    echo "⚠️ ADVERTENCIA: No hay semestre activo\n";
    echo "─────────────────────────────────────────────────────────────\n";
    echo "Deberías activar un semestre para poder ver:\n";
    echo "  • Horarios en el Dashboard\n";
    echo "  • Asistencias del período actual\n";
    echo "  • Reportes del semestre en curso\n\n";
}

echo "═══════════════════════════════════════════════════════════\n\n";

// 6. Instrucciones
echo "💡 INSTRUCCIONES:\n";
echo "─────────────────────────────────────────────────────────────\n\n";

echo "1️⃣  PARA ELIMINAR UN SEMESTRE:\n";
echo "   • Ve a: http://127.0.0.1:8000/semestres\n";
echo "   • Busca el semestre que deseas eliminar\n";
echo "   • Verifica que aparezca el botón 'Eliminar' en rojo\n";
echo "   • Si NO aparece, verifica las condiciones:\n";
echo "     - No debe ser el semestre activo\n";
echo "     - No debe tener grupos asociados\n\n";

echo "2️⃣  SI EL SEMESTRE ES ACTIVO:\n";
echo "   • Ve a: http://127.0.0.1:8000/semestres/{id}/edit\n";
echo "   • Cambia el estado a 'Planificación' o 'Terminado'\n";
echo "   • Guarda los cambios\n";
echo "   • Luego podrás eliminarlo\n\n";

echo "3️⃣  SI EL SEMESTRE TIENE GRUPOS:\n";
echo "   Opción A - Eliminar grupos:\n";
echo "     • Ve a: http://127.0.0.1:8000/grupos\n";
echo "     • Elimina cada grupo del semestre\n";
echo "     • Luego elimina el semestre\n\n";
echo "   Opción B - Reasignar grupos:\n";
echo "     • Ve a: http://127.0.0.1:8000/grupos\n";
echo "     • Edita cada grupo\n";
echo "     • Asigna un nuevo semestre\n";
echo "     • Luego elimina el semestre original\n\n";

echo "4️⃣  VERIFICAR CAMBIOS:\n";
echo "   • Ejecuta nuevamente: php scripts/verificar-semestres.php\n";
echo "   • O actualiza la página: http://127.0.0.1:8000/semestres\n\n";

echo "═══════════════════════════════════════════════════════════\n";
echo "  VERIFICACIÓN COMPLETADA\n";
echo "═══════════════════════════════════════════════════════════\n";
