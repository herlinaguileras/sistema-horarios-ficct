<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Role;
use App\Models\RoleModule;

echo "\n";
echo "══════════════════════════════════════════════════════════════\n";
echo "  ASIGNAR TODOS LOS MÓDULOS AL ROL ADMIN\n";
echo "══════════════════════════════════════════════════════════════\n\n";

// 1. Buscar rol admin
$admin = Role::where('name', 'admin')->first();

if (!$admin) {
    echo "❌ Rol 'admin' no encontrado\n\n";
    exit(1);
}

echo "✓ Rol 'admin' encontrado (ID: {$admin->id})\n\n";

// 2. Eliminar módulos anteriores
$admin->modules()->delete();
echo "✓ Módulos anteriores eliminados\n\n";

// 3. Obtener todos los módulos disponibles
$availableModules = RoleModule::availableModules();

echo "📦 Asignando todos los módulos disponibles:\n\n";

// 4. Crear registro para cada módulo
foreach ($availableModules as $moduleKey => $moduleInfo) {
    $admin->modules()->create([
        'module_name' => $moduleKey
    ]);
    echo "  ✓ {$moduleInfo['name']}\n";
}

echo "\n";
echo "══════════════════════════════════════════════════════════════\n";
echo "  VERIFICACIÓN\n";
echo "══════════════════════════════════════════════════════════════\n\n";

$modulesCount = $admin->modules()->count();
echo "✅ Total de módulos asignados al rol 'admin': {$modulesCount}\n\n";

echo "Módulos asignados:\n";
foreach ($admin->modules as $module) {
    $info = RoleModule::availableModules()[$module->module_name] ?? null;
    if ($info) {
        echo "  • {$info['name']}\n";
    }
}

echo "\n";
echo "══════════════════════════════════════════════════════════════\n";
echo "  ✅ COMPLETADO EXITOSAMENTE\n";
echo "══════════════════════════════════════════════════════════════\n\n";

echo "💡 Ahora los usuarios con rol 'admin' tendrán acceso a todos los módulos\n";
echo "💡 Recarga el navegador para ver los cambios\n\n";
