<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "\n";
echo "══════════════════════════════════════════════════════════════\n";
echo "  VERIFICAR DATOS DE USUARIOS\n";
echo "══════════════════════════════════════════════════════════════\n\n";

// 1. Contar usuarios
$totalUsers = User::count();
echo "Total de usuarios en la BD: {$totalUsers}\n\n";

// 2. Listar usuarios con sus roles
$users = User::with('roles', 'docente')->orderBy('created_at', 'desc')->get();

echo "LISTA DE USUARIOS:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Nombre: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Roles: " . ($user->roles->count() > 0 ? $user->roles->pluck('name')->implode(', ') : 'NINGUNO') . "\n";
    echo "Docente: " . ($user->docente ? $user->docente->codigo_docente : 'NO') . "\n";
    echo "Activo: " . ($user->is_active ? 'SÍ' : 'NO') . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
}

echo "\n";
echo "SIMULACIÓN DEL CONTROLADOR:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Simular lo que hace el controlador
$usersPaginated = User::with('roles', 'docente')->orderBy('created_at', 'desc')->paginate(15);

echo "Total items: {$usersPaginated->total()}\n";
echo "Items por página: {$usersPaginated->perPage()}\n";
echo "Página actual: {$usersPaginated->currentPage()}\n";
echo "Items en esta página: {$usersPaginated->count()}\n\n";

if ($usersPaginated->count() === 0) {
    echo "❌ LA CONSULTA NO DEVUELVE USUARIOS\n";
    echo "   Esto explica por qué la tabla está vacía\n\n";
} else {
    echo "✅ La consulta devuelve {$usersPaginated->count()} usuarios\n";
    echo "   La vista debería mostrarlos\n\n";
}

echo "\n";
