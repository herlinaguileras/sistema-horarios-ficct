<?php

/**
 * Script para asignar TODOS los permisos al rol de administrador
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Role;
use App\Models\Permission;

echo "🔧 Asignando todos los permisos al rol 'admin'...\n\n";

// 1. Buscar el rol admin
$adminRole = Role::where('name', 'admin')->first();

if (!$adminRole) {
    echo "❌ Error: No se encontró el rol 'admin'\n";
    exit(1);
}

echo "✅ Rol 'admin' encontrado (ID: {$adminRole->id})\n\n";

// 2. Obtener TODOS los permisos
$allPermissions = Permission::all();

echo "📋 Total de permisos en el sistema: " . $allPermissions->count() . "\n\n";

// 3. Sincronizar todos los permisos con el rol admin
$adminRole->permissions()->sync($allPermissions->pluck('id'));

echo "✅ Todos los permisos han sido asignados al rol 'admin'\n\n";

// 4. Verificar la asignación
$assignedCount = $adminRole->permissions()->count();
echo "✅ Permisos actualmente asignados al rol 'admin': {$assignedCount}\n\n";

// 5. Mostrar lista de permisos asignados agrupados por módulo
$permissionsByModule = $adminRole->permissions()->get()->groupBy('module');

echo "📊 Permisos por módulo:\n";
echo str_repeat('=', 60) . "\n";

foreach ($permissionsByModule as $module => $permissions) {
    echo "\n🔹 {$module} ({$permissions->count()} permisos):\n";
    foreach ($permissions as $permission) {
        echo "   • {$permission->name} - {$permission->description}\n";
    }
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "✅ Proceso completado exitosamente!\n";
