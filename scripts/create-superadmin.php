<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\RoleModule;
use Illuminate\Support\Facades\Hash;

echo "\n";
echo "══════════════════════════════════════════════════════════════\n";
echo "  CREAR SUPERADMIN CON TODOS LOS MÓDULOS\n";
echo "══════════════════════════════════════════════════════════════\n\n";

// 1. Verificar si existe el rol admin
$adminRole = Role::where('name', 'admin')->first();

if (!$adminRole) {
    echo "📦 Creando rol 'admin'...\n";
    $adminRole = Role::create([
        'name' => 'admin',
        'description' => 'Administrador del Sistema',
        'level' => 100,
        'status' => 'Activo',
    ]);
    echo "✓ Rol 'admin' creado\n\n";
} else {
    echo "✓ Rol 'admin' encontrado (ID: {$adminRole->id})\n\n";
}

// 2. Asignar TODOS los módulos al rol admin
echo "📦 Asignando todos los módulos al rol 'admin'...\n";
$adminRole->modules()->delete(); // Limpiar módulos anteriores

$availableModules = RoleModule::availableModules();
foreach ($availableModules as $moduleKey => $moduleInfo) {
    $adminRole->modules()->create([
        'module_name' => $moduleKey
    ]);
    echo "  ✓ {$moduleInfo['name']}\n";
}

echo "\n✓ {$adminRole->modules()->count()} módulos asignados al rol 'admin'\n\n";

// 3. Crear nuevo usuario SuperAdmin
echo "👤 Creando nuevo usuario SuperAdmin...\n\n";

// Verificar si ya existe
$existingSuperAdmin = User::where('email', 'superadmin@ficct.edu.bo')->first();
if ($existingSuperAdmin) {
    echo "⚠️  Ya existe un usuario con email 'superadmin@ficct.edu.bo'\n";
    echo "   Eliminando usuario anterior...\n";
    $existingSuperAdmin->roles()->detach();
    $existingSuperAdmin->delete();
    echo "   ✓ Usuario anterior eliminado\n\n";
}

// Crear nuevo usuario
$superAdmin = User::create([
    'name' => 'Super Administrador',
    'email' => 'superadmin@ficct.edu.bo',
    'password' => Hash::make('admin123'),
    'email_verified_at' => now(),
]);

echo "✓ Usuario creado:\n";
echo "   Nombre: {$superAdmin->name}\n";
echo "   Email: {$superAdmin->email}\n";
echo "   Password: admin123\n\n";

// 4. Asignar rol admin al usuario
$superAdmin->roles()->attach($adminRole->id);
echo "✓ Rol 'admin' asignado al usuario\n\n";

// 5. Verificación
echo "══════════════════════════════════════════════════════════════\n";
echo "  VERIFICACIÓN\n";
echo "══════════════════════════════════════════════════════════════\n\n";

$superAdmin->load('roles.modules');
echo "Usuario: {$superAdmin->name}\n";
echo "Email: {$superAdmin->email}\n";
echo "Roles: " . $superAdmin->roles->pluck('name')->implode(', ') . "\n";
echo "Módulos disponibles: {$superAdmin->roles->flatMap(fn($r) => $r->modules)->count()}\n\n";

echo "Módulos:\n";
foreach ($superAdmin->roles->first()->modules as $module) {
    $info = RoleModule::availableModules()[$module->module_name] ?? null;
    if ($info) {
        echo "  ✓ {$info['name']}\n";
    }
}

echo "\n";
echo "══════════════════════════════════════════════════════════════\n";
echo "  ✅ SUPERADMIN CREADO EXITOSAMENTE\n";
echo "══════════════════════════════════════════════════════════════\n\n";

echo "🔐 CREDENCIALES DE ACCESO:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "   Email: superadmin@ficct.edu.bo\n";
echo "   Password: admin123\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "💡 PRÓXIMOS PASOS:\n";
echo "  1. Cerrar sesión actual\n";
echo "  2. Iniciar sesión con las nuevas credenciales\n";
echo "  3. Acceder al Dashboard\n";
echo "  4. Verificar que todos los módulos estén disponibles\n\n";

echo "⚠️  IMPORTANTE:\n";
echo "  • Cambia la contraseña después del primer inicio de sesión\n";
echo "  • Este usuario tiene acceso COMPLETO al sistema\n\n";
