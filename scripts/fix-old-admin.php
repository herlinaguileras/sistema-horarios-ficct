<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;

echo "\n";
echo "══════════════════════════════════════════════════════════════\n";
echo "  ARREGLAR USUARIO ADMIN ANTERIOR\n";
echo "══════════════════════════════════════════════════════════════\n\n";

// Buscar el usuario admin anterior
$oldAdmin = User::where('email', 'admin@ficct.edu.bo')->first();

if (!$oldAdmin) {
    echo "⚠️  Usuario 'admin@ficct.edu.bo' no encontrado\n\n";
    exit(0);
}

echo "✓ Usuario encontrado: {$oldAdmin->name} ({$oldAdmin->email})\n\n";

// Verificar si tiene rol admin
$adminRole = Role::where('name', 'admin')->first();

if (!$adminRole) {
    echo "❌ Rol 'admin' no existe\n\n";
    exit(1);
}

// Verificar si el usuario ya tiene el rol
$hasAdminRole = $oldAdmin->roles->contains('id', $adminRole->id);

if ($hasAdminRole) {
    echo "✓ El usuario ya tiene el rol 'admin' asignado\n";
    echo "✓ Módulos disponibles: {$oldAdmin->roles->flatMap(fn($r) => $r->modules)->count()}\n\n";
} else {
    echo "⚠️  El usuario NO tiene el rol 'admin' asignado\n";
    echo "   Asignando rol...\n";
    $oldAdmin->roles()->attach($adminRole->id);
    echo "   ✓ Rol 'admin' asignado\n\n";
}

// Verificación final
$oldAdmin->load('roles.modules');

echo "══════════════════════════════════════════════════════════════\n";
echo "  VERIFICACIÓN FINAL\n";
echo "══════════════════════════════════════════════════════════════\n\n";

echo "Usuario: {$oldAdmin->name}\n";
echo "Email: {$oldAdmin->email}\n";
echo "Roles: " . $oldAdmin->roles->pluck('name')->implode(', ') . "\n";
echo "Módulos: {$oldAdmin->roles->flatMap(fn($r) => $r->modules)->count()}\n\n";

echo "✅ Usuario admin anterior corregido\n\n";
