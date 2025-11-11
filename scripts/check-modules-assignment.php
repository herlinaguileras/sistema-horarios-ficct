<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\RoleModule;

echo "\n";
echo "══════════════════════════════════════════════════════════════\n";
echo "  DIAGNÓSTICO: USUARIOS, ROLES Y MÓDULOS\n";
echo "══════════════════════════════════════════════════════════════\n\n";

// 1. Verificar usuarios
echo "👤 USUARIOS:\n";
$users = User::with('roles.modules')->get();
foreach ($users as $user) {
    echo "  • {$user->name} ({$user->email})\n";
    echo "    Roles: " . $user->roles->pluck('name')->implode(', ') . "\n";
    $modules = $user->roles->flatMap(fn($r) => $r->modules)->pluck('module_name')->unique();
    echo "    Módulos: " . ($modules->count() > 0 ? $modules->implode(', ') : '❌ NINGUNO') . "\n\n";
}

// 2. Verificar roles
echo "\n🛡️  ROLES:\n";
$roles = Role::with('modules')->get();
foreach ($roles as $role) {
    echo "  • {$role->name} (nivel {$role->level})\n";
    echo "    Módulos asignados: {$role->modules->count()}\n";
    if ($role->modules->count() > 0) {
        echo "    " . $role->modules->pluck('module_name')->implode(', ') . "\n";
    } else {
        echo "    ❌ NO TIENE MÓDULOS ASIGNADOS\n";
    }
    echo "\n";
}

// 3. Módulos disponibles
echo "\n📦 MÓDULOS DISPONIBLES:\n";
$availableModules = RoleModule::availableModules();
foreach ($availableModules as $key => $module) {
    echo "  • {$module['name']}: {$module['description']}\n";
}

echo "\n";
echo "══════════════════════════════════════════════════════════════\n";
echo "  PROBLEMA DETECTADO\n";
echo "══════════════════════════════════════════════════════════════\n\n";

$usersWithoutModules = $users->filter(function($user) {
    return $user->roles->flatMap(fn($r) => $r->modules)->count() === 0;
});

if ($usersWithoutModules->count() > 0) {
    echo "❌ {$usersWithoutModules->count()} usuario(s) SIN módulos asignados:\n";
    foreach ($usersWithoutModules as $user) {
        echo "  • {$user->name} ({$user->email})\n";
    }
    echo "\n";
    echo "💡 SOLUCIÓN: Asignar módulos al rol del usuario\n";
    echo "   Ejecutar: php scripts/assign-all-modules-to-admin.php\n\n";
} else {
    echo "✅ Todos los usuarios tienen módulos asignados\n\n";
}
