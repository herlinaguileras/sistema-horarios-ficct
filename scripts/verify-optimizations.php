<?php

/**
 * Script de verificación post-optimización
 * 
 * Verifica que todas las correcciones se aplicaron exitosamente
 * y que el proyecto está funcionando correctamente.
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  VERIFICACIÓN POST-OPTIMIZACIÓN                              ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

$allPassed = true;
$errors = [];
$warnings = [];

// 1. Verificar que NO existen tablas del sistema antiguo
echo "📋 VERIFICACIÓN 1: Tablas de Permisos Eliminadas\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$oldTables = ['permissions', 'permission_role'];
foreach ($oldTables as $table) {
    if (Schema::hasTable($table)) {
        echo "  ✗ Tabla '$table' AÚN EXISTE\n";
        $errors[] = "Tabla '$table' debería haber sido eliminada";
        $allPassed = false;
    } else {
        echo "  ✓ Tabla '$table' eliminada correctamente\n";
    }
}

echo "\n";

// 2. Verificar que existe la tabla de módulos
echo "📋 VERIFICACIÓN 2: Sistema de Módulos Activo\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

if (Schema::hasTable('role_modules')) {
    echo "  ✓ Tabla 'role_modules' existe\n";
    $modulesCount = DB::table('role_modules')->count();
    echo "  ✓ Módulos asignados: $modulesCount\n";
} else {
    echo "  ✗ Tabla 'role_modules' NO EXISTE\n";
    $errors[] = "Tabla 'role_modules' no encontrada";
    $allPassed = false;
}

echo "\n";

// 3. Verificar estados de asistencia
echo "📋 VERIFICACIÓN 3: Estados de Asistencia Válidos\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

if (Schema::hasTable('asistencias')) {
    $invalidEstados = DB::table('asistencias')
        ->whereRaw("estado != LOWER(estado)")
        ->orWhereNotIn('estado', ['presente', 'ausente', 'tardanza'])
        ->count();
    
    if ($invalidEstados > 0) {
        echo "  ✗ Encontrados $invalidEstados registros con estados inválidos\n";
        $errors[] = "$invalidEstados asistencias con estados inválidos";
        $allPassed = false;
    } else {
        echo "  ✓ Todos los estados de asistencia son válidos\n";
        
        $estadosCount = DB::table('asistencias')
            ->select('estado', DB::raw('count(*) as count'))
            ->groupBy('estado')
            ->get();
        
        foreach ($estadosCount as $estado) {
            echo "    • {$estado->estado}: {$estado->count}\n";
        }
    }
} else {
    echo "  ⚠ Tabla 'asistencias' no existe (aún no hay datos)\n";
    $warnings[] = "Tabla asistencias no existe";
}

echo "\n";

// 4. Verificar archivos eliminados
echo "📋 VERIFICACIÓN 4: Archivos Obsoletos Eliminados\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$shouldNotExist = [
    'app/Http/Middleware/CheckPermission.php',
    'app/Models/Permission.php',
    'database/migrations/2025_10_26_223930_create_permissions_table.php',
    'database/migrations/2025_10_26_224350_create_permission_role_table.php',
    'check-users.php', // Debería estar en scripts/
    'analyze-project.php', // Debería haber sido eliminado
];

foreach ($shouldNotExist as $file) {
    $fullPath = __DIR__ . '/../' . $file;
    if (file_exists($fullPath)) {
        echo "  ✗ Archivo '$file' AÚN EXISTE\n";
        $warnings[] = "Archivo '$file' debería haber sido eliminado o movido";
    } else {
        echo "  ✓ Archivo '$file' eliminado/movido\n";
    }
}

echo "\n";

// 5. Verificar estructura de directorios
echo "📋 VERIFICACIÓN 5: Estructura de Directorios\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$shouldExist = [
    'scripts',
    'scripts/obsolete',
];

foreach ($shouldExist as $dir) {
    $fullPath = __DIR__ . '/../' . $dir;
    if (is_dir($fullPath)) {
        echo "  ✓ Directorio '$dir' existe\n";
        
        if ($dir === 'scripts/obsolete') {
            $obsoleteFiles = count(glob($fullPath . '/*.php'));
            echo "    • Archivos archivados: $obsoleteFiles\n";
        }
    } else {
        echo "  ✗ Directorio '$dir' NO EXISTE\n";
        $warnings[] = "Directorio '$dir' no encontrado";
    }
}

echo "\n";

// 6. Verificar integridad de la base de datos
echo "📋 VERIFICACIÓN 6: Integridad de la Base de Datos\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$tables = ['users', 'roles', 'role_user', 'role_modules'];
foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        $count = DB::table($table)->count();
        echo "  ✓ Tabla '$table': $count registros\n";
    } else {
        echo "  ✗ Tabla '$table' NO EXISTE\n";
        $errors[] = "Tabla crítica '$table' no encontrada";
        $allPassed = false;
    }
}

echo "\n";

// 7. Verificar usuarios sin roles
echo "📋 VERIFICACIÓN 7: Usuarios con Roles Asignados\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

if (Schema::hasTable('users') && Schema::hasTable('role_user')) {
    $usersWithoutRole = DB::table('users')
        ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
        ->whereNull('role_user.user_id')
        ->count();
    
    if ($usersWithoutRole > 0) {
        echo "  ⚠ Hay $usersWithoutRole usuarios sin rol asignado\n";
        $warnings[] = "$usersWithoutRole usuarios sin rol";
    } else {
        echo "  ✓ Todos los usuarios tienen roles asignados\n";
    }
}

echo "\n";

// RESUMEN FINAL
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  RESUMEN DE VERIFICACIÓN                                     ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

if ($allPassed && count($warnings) === 0) {
    echo "🎉 ¡TODAS LAS VERIFICACIONES PASARON EXITOSAMENTE!\n";
    echo "\n";
    echo "✅ Sistema optimizado correctamente\n";
    echo "✅ Sin errores críticos\n";
    echo "✅ Sin advertencias\n";
    echo "\n";
} elseif ($allPassed) {
    echo "✅ VERIFICACIÓN COMPLETADA CON ADVERTENCIAS\n";
    echo "\n";
    echo "Sin errores críticos, pero hay algunas advertencias:\n\n";
    foreach ($warnings as $warning) {
        echo "  ⚠ $warning\n";
    }
    echo "\n";
} else {
    echo "❌ VERIFICACIÓN COMPLETADA CON ERRORES\n";
    echo "\n";
    if (count($errors) > 0) {
        echo "Errores críticos encontrados:\n\n";
        foreach ($errors as $error) {
            echo "  ✗ $error\n";
        }
        echo "\n";
    }
    if (count($warnings) > 0) {
        echo "Advertencias encontradas:\n\n";
        foreach ($warnings as $warning) {
            echo "  ⚠ $warning\n";
        }
        echo "\n";
    }
}

echo "\n";
echo "📊 ESTADÍSTICAS FINALES:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  • Errores críticos: " . count($errors) . "\n";
echo "  • Advertencias: " . count($warnings) . "\n";
echo "  • Estado general: " . ($allPassed ? "✅ OK" : "❌ REQUIERE ATENCIÓN") . "\n";
echo "\n";

exit($allPassed ? 0 : 1);
