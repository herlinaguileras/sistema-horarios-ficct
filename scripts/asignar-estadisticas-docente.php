<?php

/**
 * Asignar módulo de estadísticas al rol docente
 * Los docentes podrán ver SOLO sus propias estadísticas
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Role;
use App\Models\RoleModule;

echo "════════════════════════════════════════════════════\n";
echo "  ASIGNAR MÓDULO ESTADÍSTICAS A ROL DOCENTE\n";
echo "════════════════════════════════════════════════════\n\n";

// Buscar el rol docente
$docenteRole = Role::where('name', 'docente')->first();

if (!$docenteRole) {
    echo "❌ ERROR: Rol 'docente' no encontrado\n";
    exit(1);
}

echo "✓ Rol encontrado: {$docenteRole->name} (ID: {$docenteRole->id})\n";
echo "  Descripción: {$docenteRole->description}\n\n";

// Verificar si ya tiene el módulo estadísticas
$tieneEstadisticas = $docenteRole->modules()
    ->where('module_name', 'estadisticas')
    ->exists();

if ($tieneEstadisticas) {
    echo "ℹ️  El rol docente YA TIENE el módulo 'estadísticas' asignado\n\n";
} else {
    echo "🔧 ASIGNANDO MÓDULO 'ESTADÍSTICAS'...\n\n";
    
    // Crear la relación
    RoleModule::create([
        'role_id' => $docenteRole->id,
        'module_name' => 'estadisticas',
        'can_view' => true,
        'can_create' => false,  // NO pueden crear estadísticas
        'can_edit' => false,    // NO pueden editar
        'can_delete' => false,  // NO pueden eliminar
    ]);
    
    echo "✅ Módulo 'estadísticas' asignado con permisos de SOLO LECTURA\n\n";
}

// Mostrar configuración actual
echo "════════════════════════════════════════════════════\n";
echo "  CONFIGURACIÓN ACTUAL DEL ROL DOCENTE\n";
echo "════════════════════════════════════════════════════\n\n";

$modules = $docenteRole->modules;

if ($modules->count() === 0) {
    echo "⚠️  Sin módulos asignados\n";
} else {
    echo "Total de módulos: {$modules->count()}\n\n";
    
    foreach ($modules as $module) {
        echo "📊 {$module->module_name}\n";
        
        $permisos = [];
        if ($module->can_view) $permisos[] = 'Ver';
        if ($module->can_create) $permisos[] = 'Crear';
        if ($module->can_edit) $permisos[] = 'Editar';
        if ($module->can_delete) $permisos[] = 'Eliminar';
        
        echo "   Permisos: " . implode(', ', $permisos) . "\n";
        
        // Descripción especial para estadísticas
        if ($module->module_name === 'estadisticas') {
            echo "   ℹ️  Los docentes solo pueden ver sus PROPIAS estadísticas\n";
        }
        
        echo "\n";
    }
}

echo "════════════════════════════════════════════════════\n";
echo "  RESTRICCIONES DE SEGURIDAD\n";
echo "════════════════════════════════════════════════════\n\n";

echo "✓ Docentes PUEDEN:\n";
echo "  • Ver sus propias estadísticas personales\n";
echo "  • Ver su historial de asistencias registradas\n";
echo "  • Ver sus grupos, materias y horarios\n";
echo "  • Ver gráficos de su rendimiento\n\n";

echo "✗ Docentes NO PUEDEN:\n";
echo "  • Ver estadísticas de otros docentes\n";
echo "  • Ver el listado general de todos los docentes\n";
echo "  • Crear, editar o eliminar estadísticas\n";
echo "  • Acceder a información administrativa\n\n";

echo "════════════════════════════════════════════════════\n\n";

echo "✅ CONFIGURACIÓN COMPLETADA\n\n";
echo "Los docentes ahora pueden:\n";
echo "  1. Acceder a /estadisticas (serán redirigidos a sus propias estadísticas)\n";
echo "  2. Ver /estadisticas/{su-id} (solo su propio ID)\n";
echo "  3. El sistema bloqueará intentos de ver estadísticas de otros\n\n";
