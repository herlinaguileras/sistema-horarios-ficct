<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Role;
use App\Models\Permission;

echo "\n=== VERIFICACIÓN DE PERMISOS POR ROL ===\n\n";

// Obtener todos los roles
$roles = Role::with('permissions')->get();

foreach ($roles as $role) {
    echo "ROL: {$role->name} (ID: {$role->id})\n";
    echo str_repeat('-', 50) . "\n";

    $permissions = $role->permissions;
    echo "Total de permisos: " . $permissions->count() . "\n\n";

    if ($permissions->count() > 0) {
        // Agrupar por módulo
        $grouped = [];
        foreach ($permissions as $perm) {
            // Extraer el módulo del nombre del permiso (ej: ver_usuarios -> usuarios)
            $parts = explode('_', $perm->name);
            $module = count($parts) > 1 ? $parts[1] : 'otros';

            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            $grouped[$module][] = $perm->name;
        }

        foreach ($grouped as $module => $perms) {
            echo "  📁 " . ucfirst($module) . ":\n";
            foreach ($perms as $perm) {
                echo "    ✓ $perm\n";
            }
            echo "\n";
        }
    } else {
        echo "  ⚠️  No tiene permisos asignados\n\n";
    }

    echo "\n";
}

// Verificar específicamente el rol coordinador
echo "\n=== VERIFICACIÓN ESPECÍFICA DEL ROL 'coordinador' ===\n\n";
$coordinador = Role::where('name', 'coordinador')->first();

if ($coordinador) {
    echo "✓ Rol encontrado (ID: {$coordinador->id})\n";
    echo "  Permisos: " . $coordinador->permissions->count() . "\n\n";

    if ($coordinador->permissions->count() > 0) {
        echo "  Lista de permisos:\n";
        foreach ($coordinador->permissions as $perm) {
            echo "    • {$perm->name} - {$perm->description}\n";
        }
    }
} else {
    echo "⚠️  Rol 'coordinador' no encontrado\n";
}

echo "\n=== FIN DE VERIFICACIÓN ===\n\n";
