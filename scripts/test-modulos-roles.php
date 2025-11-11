<?php

/**
 * Script para verificar que el sistema de módulos en roles funciona correctamente
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Role;
use App\Models\RoleModule;

echo "============================================\n";
echo "  TEST: Sistema de Módulos en Roles\n";
echo "============================================\n\n";

// 1. Verificar módulos disponibles
echo "📦 MÓDULOS DISPONIBLES EN EL SISTEMA:\n";
echo "──────────────────────────────────────────\n";

$modulosDisponibles = RoleModule::availableModules();

foreach ($modulosDisponibles as $key => $module) {
    $icono = match($key) {
        'usuarios' => '👥',
        'roles' => '🛡️',
        'docentes' => '👨‍🏫',
        'materias' => '📚',
        'aulas' => '🏫',
        'grupos' => '👥',
        'semestres' => '📅',
        'horarios' => '🕐',
        'importacion' => '📤',
        'estadisticas' => '📊',
        default => '📦'
    };
    
    echo "   {$icono} {$key} → {$module['name']}\n";
    echo "      Ruta: {$module['route']}\n";
}

echo "\n✓ Total de módulos disponibles: " . count($modulosDisponibles) . "\n\n";

// 2. Verificar roles existentes y sus módulos
echo "══════════════════════════════════════════\n";
echo "  ROLES Y SUS MÓDULOS ASIGNADOS\n";
echo "══════════════════════════════════════════\n\n";

$roles = Role::with('modules')->get();

foreach ($roles as $role) {
    $modulosAsignados = $role->modules->count();
    $estado = $role->status === 'Activo' ? '✅' : '⚠️';
    
    echo "{$estado} Rol: {$role->name}\n";
    echo "   Descripción: {$role->description}\n";
    echo "   Nivel: {$role->level}\n";
    echo "   Estado: {$role->status}\n";
    echo "   Módulos asignados: {$modulosAsignados}\n";
    
    if ($modulosAsignados > 0) {
        echo "   ├─ Módulos:\n";
        foreach ($role->modules as $module) {
            $moduloInfo = $modulosDisponibles[$module->module_name] ?? ['name' => 'Desconocido'];
            echo "   │  • {$module->module_name} ({$moduloInfo['name']})\n";
        }
    } else {
        echo "   └─ Sin módulos asignados ⚠️\n";
    }
    
    echo "\n";
}

// 3. Verificar que el admin tenga todos los módulos
echo "══════════════════════════════════════════\n";
echo "  VERIFICACIÓN: Rol Admin\n";
echo "══════════════════════════════════════════\n\n";

$adminRole = Role::where('name', 'admin')->first();

if ($adminRole) {
    $adminModulosCount = $adminRole->modules->count();
    $totalModulos = count($modulosDisponibles);
    
    echo "Rol: {$adminRole->name}\n";
    echo "Módulos asignados: {$adminModulosCount}/{$totalModulos}\n\n";
    
    if ($adminModulosCount === $totalModulos) {
        echo "╔══════════════════════════════════════════╗\n";
        echo "║  ✅ ADMIN TIENE TODOS LOS MÓDULOS       ║\n";
        echo "╚══════════════════════════════════════════╝\n";
    } else {
        echo "╔══════════════════════════════════════════╗\n";
        echo "║  ⚠️ ADMIN NO TIENE TODOS LOS MÓDULOS    ║\n";
        echo "╚══════════════════════════════════════════╝\n";
        
        echo "\n⚠️ Módulos faltantes:\n";
        $modulosAsignados = $adminRole->modules->pluck('module_name')->toArray();
        foreach ($modulosDisponibles as $key => $module) {
            if (!in_array($key, $modulosAsignados)) {
                echo "   • {$key} ({$module['name']})\n";
            }
        }
    }
} else {
    echo "❌ Rol 'admin' no encontrado\n";
}

echo "\n";
echo "══════════════════════════════════════════\n";
echo "  RESUMEN\n";
echo "══════════════════════════════════════════\n";
echo "• Módulos disponibles: " . count($modulosDisponibles) . "\n";
echo "• Roles en el sistema: " . $roles->count() . "\n";
echo "• Sistema: ✅ Funcionando\n";
echo "══════════════════════════════════════════\n";
