<?php

/**
 * Script para verificar y asignar módulos al rol docente
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Role;
use App\Models\RoleModule;

echo "============================================\n";
echo "  VERIFICAR MÓDULOS DEL ROL DOCENTE\n";
echo "============================================\n\n";

// Obtener el rol docente
$docenteRole = Role::where('name', 'docente')->first();

if (!$docenteRole) {
    echo "❌ ERROR: No se encontró el rol 'docente'\n";
    exit(1);
}

echo "✓ Rol: {$docenteRole->name}\n";
echo "  Descripción: {$docenteRole->description}\n";
echo "  Estado: {$docenteRole->status}\n\n";

// Verificar módulos actuales
$modulosActuales = $docenteRole->modules()->get();

echo "📦 MÓDULOS ACTUALES DEL ROL DOCENTE:\n";
echo "──────────────────────────────────────────\n";

if ($modulosActuales->isEmpty()) {
    echo "⚠️ El rol docente NO tiene módulos asignados\n\n";
    
    echo "💡 ¿Deseas asignar módulos básicos para docentes? (horarios, grupos, materias)\n";
    echo "   Se asignarán automáticamente...\n\n";
    
    // Módulos sugeridos para docentes
    $modulosDocente = [
        'horarios' => 'Ver horarios y registrar asistencias',
        'grupos' => 'Ver grupos asignados',
        'materias' => 'Ver materias que imparte',
        'estadisticas' => 'Ver sus estadísticas personales'
    ];
    
    echo "🔧 ASIGNANDO MÓDULOS SUGERIDOS:\n";
    echo "──────────────────────────────────────────\n";
    
    foreach ($modulosDocente as $moduleName => $descripcion) {
        $docenteRole->modules()->create([
            'module_name' => $moduleName
        ]);
        echo "✓ Módulo '{$moduleName}' asignado - {$descripcion}\n";
    }
    
    echo "\n";
    echo "╔══════════════════════════════════════════╗\n";
    echo "║  ✅ MÓDULOS ASIGNADOS EXITOSAMENTE      ║\n";
    echo "║  Total: " . count($modulosDocente) . " módulo(s)                    ║\n";
    echo "╚══════════════════════════════════════════╝\n";
    
} else {
    echo "Total de módulos: {$modulosActuales->count()}\n\n";
    
    foreach ($modulosActuales as $module) {
        $modulosDisponibles = RoleModule::availableModules();
        $info = $modulosDisponibles[$module->module_name] ?? ['name' => 'Desconocido'];
        echo "  ✓ {$module->module_name} → {$info['name']}\n";
    }
    
    echo "\n";
    echo "╔══════════════════════════════════════════╗\n";
    echo "║  ✅ ROL DOCENTE YA TIENE MÓDULOS        ║\n";
    echo "╚══════════════════════════════════════════╝\n";
}

echo "\n";
echo "══════════════════════════════════════════\n";
echo "  RESUMEN\n";
echo "══════════════════════════════════════════\n";
echo "• Rol: docente\n";
echo "• Módulos asignados: " . $docenteRole->modules()->count() . "\n";
echo "• Estado: {$docenteRole->status}\n";
echo "══════════════════════════════════════════\n";
