<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Role;
use App\Models\RoleModule;

echo "\n=== ASIGNAR MÓDULOS AL ROL COORDINADOR ===\n\n";

// Buscar el rol coordinador
$coordinador = Role::where('name', 'coordinador')->first();

if (!$coordinador) {
    echo "⚠️  Rol 'coordinador' no encontrado. Creándolo...\n";
    $coordinador = Role::create([
        'name' => 'coordinador',
        'description' => 'Coordinador Académico',
        'level' => 10,
        'status' => 'Activo',
    ]);
    echo "✓ Rol 'coordinador' creado\n\n";
} else {
    echo "✓ Rol 'coordinador' encontrado (ID: {$coordinador->id})\n\n";
}

// Eliminar módulos anteriores
$coordinador->modules()->delete();
echo "✓ Módulos anteriores eliminados\n\n";

// Módulos a asignar
$modulesToAssign = ['horarios', 'estadisticas'];

echo "Asignando módulos:\n";
foreach ($modulesToAssign as $moduleName) {
    $coordinador->modules()->create([
        'module_name' => $moduleName
    ]);
    echo "  ✓ $moduleName\n";
}

echo "\n=== RESUMEN ===\n";
echo "Rol: {$coordinador->name}\n";
echo "Módulos asignados: " . $coordinador->modules()->count() . "\n\n";

echo "Módulos disponibles:\n";
foreach ($coordinador->modules as $module) {
    $info = RoleModule::availableModules()[$module->module_name] ?? null;
    if ($info) {
        echo "  • {$info['name']} - {$info['description']}\n";
    }
}

echo "\n✅ ¡Módulos asignados exitosamente!\n\n";
