<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "══════════════════════════════════════════════════════════════\n";
echo "  DIAGNÓSTICO COMPLETO DEL SISTEMA\n";
echo "══════════════════════════════════════════════════════════════\n\n";

// 1. Verificar tabla role_user
echo "1️⃣  TABLA role_user (relación usuarios-roles):\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$roleUserRecords = DB::table('role_user')->get();
if ($roleUserRecords->isEmpty()) {
    echo "❌ LA TABLA role_user ESTÁ VACÍA - ¡ESTE ES EL PROBLEMA!\n\n";
} else {
    foreach ($roleUserRecords as $record) {
        $user = User::find($record->user_id);
        $role = Role::find($record->role_id);
        echo "  ✓ Usuario #{$record->user_id} ({$user->name}) → Rol #{$record->role_id} ({$role->name})\n";
    }
    echo "\n";
}

// 2. Verificar todos los usuarios y sus roles
echo "2️⃣  USUARIOS Y SUS ROLES (usando Eloquent):\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$users = User::with('roles')->get();
foreach ($users as $user) {
    echo "Usuario: {$user->name} ({$user->email})\n";
    echo "  Roles asignados: " . ($user->roles->count() > 0 ? $user->roles->pluck('name')->implode(', ') : '❌ NINGUNO') . "\n";
    echo "  hasRole('admin'): " . ($user->hasRole('admin') ? '✅ TRUE' : '❌ FALSE') . "\n";
    echo "  hasModule('usuarios'): " . ($user->hasModule('usuarios') ? '✅ TRUE' : '❌ FALSE') . "\n\n";
}

// 3. Verificar roles y sus módulos
echo "3️⃣  ROLES Y SUS MÓDULOS:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$roles = Role::with('modules')->get();
foreach ($roles as $role) {
    echo "Rol: {$role->name}\n";
    echo "  Módulos: " . ($role->modules->count() > 0 ? $role->modules->pluck('module_name')->implode(', ') : '❌ NINGUNO') . "\n\n";
}

// 4. Verificar tabla role_modules
echo "4️⃣  TABLA role_modules:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$roleModulesCount = DB::table('role_modules')->count();
echo "Total de registros: {$roleModulesCount}\n";
if ($roleModulesCount > 0) {
    $roleModules = DB::table('role_modules')
        ->join('roles', 'role_modules.role_id', '=', 'roles.id')
        ->select('roles.name as role_name', 'role_modules.module_name')
        ->get();
    foreach ($roleModules as $rm) {
        echo "  {$rm->role_name} → {$rm->module_name}\n";
    }
}
echo "\n";

// 5. DIAGNÓSTICO FINAL
echo "══════════════════════════════════════════════════════════════\n";
echo "  DIAGNÓSTICO Y SOLUCIÓN\n";
echo "══════════════════════════════════════════════════════════════\n\n";

$adminUsers = User::whereHas('roles', function($q) {
    $q->where('name', 'admin');
})->get();

if ($adminUsers->isEmpty()) {
    echo "❌ PROBLEMA: NO HAY USUARIOS CON ROL ADMIN\n";
    echo "   La tabla role_user no tiene registros que asocien usuarios con el rol admin\n\n";
    echo "💡 SOLUCIÓN: Ejecutar script de corrección\n\n";
} else {
    echo "✅ Usuarios con rol admin:\n";
    foreach ($adminUsers as $user) {
        echo "  • {$user->name} ({$user->email})\n";
        echo "    hasRole('admin'): " . ($user->hasRole('admin') ? '✅ TRUE' : '❌ FALSE') . "\n";
        echo "    Módulos: " . count($user->getModules()) . "\n";
    }
    echo "\n";
}

// 6. Prueba de middleware
echo "6️⃣  SIMULACIÓN DE MIDDLEWARE:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$testUser = User::where('email', 'superadmin@ficct.edu.bo')->first()
    ?? User::where('email', 'admin@ficct.edu.bo')->first();

if ($testUser) {
    echo "Usuario de prueba: {$testUser->name}\n";
    echo "  hasRole('admin'): " . ($testUser->hasRole('admin') ? '✅ SÍ' : '❌ NO') . "\n";
    echo "  hasModule('usuarios'): " . ($testUser->hasModule('usuarios') ? '✅ SÍ' : '❌ NO') . "\n";
    echo "  hasModule('roles'): " . ($testUser->hasModule('roles') ? '✅ SÍ' : '❌ NO') . "\n\n";

    if (!$testUser->hasRole('admin')) {
        echo "❌ EL USUARIO NO TIENE EL ROL ADMIN ASIGNADO EN role_user\n";
        echo "   Por eso el middleware rechaza el acceso\n\n";
    }
}

echo "\n";
