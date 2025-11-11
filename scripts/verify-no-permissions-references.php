<?php

/**
 * Script para verificar que no quedan referencias al sistema de permisos antiguo
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  VERIFICACIÓN: SIN REFERENCIAS AL SISTEMA ANTIGUO            ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

$errors = [];

// 1. Verificar que el método permissions() no existe en Role
echo "📋 Verificando modelo Role...\n";
$role = new \App\Models\Role();
if (method_exists($role, 'permissions')) {
    echo "  ✗ El método permissions() AÚN EXISTE en Role\n";
    $errors[] = "Método permissions() en Role";
} else {
    echo "  ✓ Método permissions() eliminado de Role\n";
}

if (method_exists($role, 'hasPermission')) {
    echo "  ✗ El método hasPermission() AÚN EXISTE en Role\n";
    $errors[] = "Método hasPermission() en Role";
} else {
    echo "  ✓ Método hasPermission() eliminado de Role\n";
}

// 2. Verificar que el método hasPermission() no existe en User
echo "\n📋 Verificando modelo User...\n";
$user = new \App\Models\User();
if (method_exists($user, 'hasPermission')) {
    echo "  ✗ El método hasPermission() AÚN EXISTE en User\n";
    $errors[] = "Método hasPermission() en User";
} else {
    echo "  ✓ Método hasPermission() eliminado de User\n";
}

// 3. Verificar que no existe el modelo Permission
echo "\n📋 Verificando modelo Permission...\n";
if (class_exists('\App\Models\Permission')) {
    echo "  ✗ La clase Permission AÚN EXISTE\n";
    $errors[] = "Clase Permission existe";
} else {
    echo "  ✓ Clase Permission eliminada\n";
}

// 4. Verificar que no existe el middleware CheckPermission
echo "\n📋 Verificando middleware CheckPermission...\n";
if (class_exists('\App\Http\Middleware\CheckPermission')) {
    echo "  ✗ El middleware CheckPermission AÚN EXISTE\n";
    $errors[] = "Middleware CheckPermission existe";
} else {
    echo "  ✓ Middleware CheckPermission eliminado\n";
}

// 5. Verificar tablas en la base de datos
echo "\n📋 Verificando base de datos...\n";
if (Schema::hasTable('permissions')) {
    echo "  ✗ Tabla 'permissions' AÚN EXISTE\n";
    $errors[] = "Tabla permissions existe";
} else {
    echo "  ✓ Tabla 'permissions' eliminada\n";
}

if (Schema::hasTable('permission_role')) {
    echo "  ✗ Tabla 'permission_role' AÚN EXISTE\n";
    $errors[] = "Tabla permission_role existe";
} else {
    echo "  ✓ Tabla 'permission_role' eliminada\n";
}

// RESUMEN
echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  RESULTADO DE LA VERIFICACIÓN                                ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

if (count($errors) === 0) {
    echo "🎉 ¡VERIFICACIÓN EXITOSA!\n";
    echo "\n";
    echo "✅ No se encontraron referencias al sistema antiguo de permisos\n";
    echo "✅ El sistema de módulos está correctamente implementado\n";
    echo "✅ El proyecto está limpio y optimizado\n";
    echo "\n";
    exit(0);
} else {
    echo "❌ ERRORES ENCONTRADOS:\n";
    echo "\n";
    foreach ($errors as $error) {
        echo "  ✗ $error\n";
    }
    echo "\n";
    echo "⚠️  Todavía existen referencias al sistema antiguo que deben ser eliminadas.\n";
    echo "\n";
    exit(1);
}
