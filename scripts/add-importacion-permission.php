<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Models\Role;

try {
    echo "🔄 Creando permiso de importación masiva...\n";
    
    // Crear el permiso
    $permission = Permission::firstOrCreate(
        ['name' => 'importar_horarios_masivos'],
        [
            'description' => 'Importar horarios masivamente desde Excel/CSV',
            'module' => 'Importación'
        ]
    );
    
    echo "✅ Permiso 'importar_horarios_masivos' creado/verificado\n\n";
    
    // Asignar al rol admin
    $adminRole = Role::where('name', 'admin')->first();
    
    if ($adminRole) {
        // Verificar si ya tiene el permiso
        if (!$adminRole->permissions()->where('permission_id', $permission->id)->exists()) {
            $adminRole->permissions()->attach($permission->id);
            echo "✅ Permiso agregado al rol 'admin'\n";
        } else {
            echo "ℹ️  El rol 'admin' ya tiene este permiso\n";
        }
    } else {
        echo "⚠️  No se encontró el rol 'admin'\n";
    }
    
    echo "\n✅ Proceso completado!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
