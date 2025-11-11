<?php

/**
 * Script para limpiar el sistema de permisos antiguo
 * 
 * Este script elimina las tablas y código relacionado con el sistema
 * de permisos antiguo (permissions, permission_role) ya que el proyecto
 * ahora usa el sistema de módulos (role_modules).
 * 
 * ADVERTENCIA: Este script hace cambios irreversibles en la base de datos.
 * Asegúrate de tener un backup antes de ejecutarlo.
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  LIMPIEZA DEL SISTEMA DE PERMISOS ANTIGUO                    ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

// 1. Verificar estado actual
echo "📊 ESTADO ACTUAL:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$permissionsExist = Schema::hasTable('permissions');
$permissionRoleExist = Schema::hasTable('permission_role');
$roleModulesExist = Schema::hasTable('role_modules');

echo "  • Tabla 'permissions': " . ($permissionsExist ? "✓ Existe" : "✗ No existe") . "\n";
echo "  • Tabla 'permission_role': " . ($permissionRoleExist ? "✓ Existe" : "✗ No existe") . "\n";
echo "  • Tabla 'role_modules': " . ($roleModulesExist ? "✓ Existe" : "✗ No existe") . "\n";

if ($permissionsExist) {
    $permissionsCount = DB::table('permissions')->count();
    echo "  • Total de permisos: $permissionsCount\n";
}

if ($permissionRoleExist) {
    $relationsCount = DB::table('permission_role')->count();
    echo "  • Total de relaciones: $relationsCount\n";
}

if ($roleModulesExist) {
    $modulesCount = DB::table('role_modules')->count();
    echo "  • Total de módulos asignados: $modulesCount\n";
}

echo "\n";

// 2. Confirmación
echo "⚠️  ADVERTENCIA:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  Este script eliminará:\n";
echo "    1. La tabla 'permissions'\n";
echo "    2. La tabla 'permission_role'\n";
echo "    3. Las migraciones relacionadas\n";
echo "\n";
echo "  El sistema de módulos (role_modules) se mantendrá intacto.\n";
echo "\n";

// Modo automático para ejecución sin intervención
$autoMode = in_array('--auto', $argv);

if (!$autoMode) {
    echo "  ¿Deseas continuar? (si/no): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $confirmation = trim(strtolower($line));
    fclose($handle);
    
    if ($confirmation !== 'si' && $confirmation !== 's' && $confirmation !== 'yes' && $confirmation !== 'y') {
        echo "\n❌ Operación cancelada.\n\n";
        exit(0);
    }
}

echo "\n";
echo "🔧 EJECUTANDO LIMPIEZA:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

try {
    DB::beginTransaction();
    
    // 3. Eliminar tablas en orden correcto (respetando foreign keys)
    if ($permissionRoleExist) {
        echo "  → Eliminando tabla 'permission_role'... ";
        Schema::dropIfExists('permission_role');
        echo "✓\n";
    }
    
    if ($permissionsExist) {
        echo "  → Eliminando tabla 'permissions'... ";
        Schema::dropIfExists('permissions');
        echo "✓\n";
    }
    
    // 4. Eliminar registros de migraciones relacionadas
    echo "  → Eliminando registros de migraciones... ";
    DB::table('migrations')
        ->where('migration', 'LIKE', '%permissions%')
        ->orWhere('migration', 'LIKE', '%permission_role%')
        ->delete();
    echo "✓\n";
    
    DB::commit();
    
    echo "\n";
    echo "✅ LIMPIEZA COMPLETADA EXITOSAMENTE\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\n";
    
    // 5. Estado final
    echo "📊 ESTADO FINAL:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "  • Sistema de permisos antiguo: ✗ Eliminado\n";
    echo "  • Sistema de módulos: ✓ Activo\n";
    
    if ($roleModulesExist) {
        $modulesCountFinal = DB::table('role_modules')->count();
        echo "  • Módulos asignados: $modulesCountFinal\n";
    }
    
    echo "\n";
    echo "📝 PRÓXIMOS PASOS:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "  1. Eliminar archivos de código:\n";
    echo "     • app/Http/Middleware/CheckPermission.php\n";
    echo "     • Métodos hasPermission() y hasPermissions() de User.php\n";
    echo "     • Método permissions() de Role.php\n";
    echo "     • Relaciones permissions en modelos\n";
    echo "\n";
    echo "  2. Eliminar migraciones de archivos:\n";
    echo "     • database/migrations/*_create_permissions_table.php\n";
    echo "     • database/migrations/*_create_permission_role_table.php\n";
    echo "\n";
    echo "  3. Actualizar documentación del proyecto\n";
    echo "\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "\n";
    echo "❌ ERROR DURANTE LA LIMPIEZA:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "  " . $e->getMessage() . "\n";
    echo "\n";
    exit(1);
}

echo "\n";
