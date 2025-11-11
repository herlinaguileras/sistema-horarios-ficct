<?php

/**
 * Script para verificar materias y probar rutas
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Materia;

echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║  VERIFICACIÓN DE MATERIAS Y RUTAS                        ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n\n";

// 1. Verificar materias en la base de datos
echo "📊 MATERIAS EN BASE DE DATOS:\n";
echo "─────────────────────────────────────────────────────────────\n\n";

$materias = Materia::with('carreras')->get();

if ($materias->isEmpty()) {
    echo "❌ No hay materias registradas en la base de datos.\n";
    echo "   Por favor, crea al menos una materia primero.\n\n";
} else {
    echo "Total de materias: {$materias->count()}\n\n";
    
    foreach ($materias as $materia) {
        echo "ID: {$materia->id}\n";
        echo "Nombre: {$materia->nombre}\n";
        echo "Sigla: {$materia->sigla}\n";
        echo "Nivel: {$materia->nivel_semestre}\n";
        echo "Carreras: " . $materia->carreras->pluck('nombre')->implode(', ') . "\n";
        
        // Generar URLs de prueba
        echo "\n🔗 URLs para esta materia:\n";
        echo "   • Editar: " . route('materias.edit', $materia) . "\n";
        echo "   • Eliminar: " . route('materias.destroy', $materia) . " (DELETE)\n";
        echo "\n" . str_repeat("─", 60) . "\n\n";
    }
}

// 2. Verificar rutas registradas
echo "🛣️  RUTAS REGISTRADAS:\n";
echo "─────────────────────────────────────────────────────────────\n\n";

$routes = [
    'materias.index' => route('materias.index'),
    'materias.create' => route('materias.create'),
];

if ($materias->isNotEmpty()) {
    $primeraMateria = $materias->first();
    $routes['materias.edit (ID: ' . $primeraMateria->id . ')'] = route('materias.edit', $primeraMateria);
    $routes['materias.update (ID: ' . $primeraMateria->id . ')'] = route('materias.update', $primeraMateria);
    $routes['materias.destroy (ID: ' . $primeraMateria->id . ')'] = route('materias.destroy', $primeraMateria);
}

foreach ($routes as $nombre => $url) {
    echo "✓ {$nombre}\n";
    echo "  → {$url}\n\n";
}

echo "═══════════════════════════════════════════════════════════\n\n";

// 3. Verificar permisos del usuario actual
echo "👤 VERIFICACIÓN DE PERMISOS:\n";
echo "─────────────────────────────────────────────────────────────\n\n";

$usuarios = \App\Models\User::with('roles')->get();

foreach ($usuarios as $usuario) {
    $tieneModulo = $usuario->hasModule('materias');
    $esAdmin = $usuario->hasRole('admin');
    
    echo "Usuario: {$usuario->name}\n";
    echo "Email: {$usuario->email}\n";
    echo "Rol: " . $usuario->roles->pluck('name')->implode(', ') . "\n";
    echo "¿Es Admin?: " . ($esAdmin ? "✅ SÍ" : "❌ NO") . "\n";
    echo "¿Tiene acceso a 'materias'?: " . ($tieneModulo ? "✅ SÍ" : "❌ NO") . "\n\n";
}

echo "═══════════════════════════════════════════════════════════\n\n";

// 4. Consejos
echo "💡 CONSEJOS PARA RESOLVER PROBLEMAS:\n";
echo "─────────────────────────────────────────────────────────────\n\n";

echo "1. Si aparece 'Page Not Found' (404):\n";
echo "   • Verifica que la materia existe en la base de datos\n";
echo "   • Verifica que el ID en la URL es correcto\n";
echo "   • Ejecuta: php artisan route:clear\n";
echo "   • Ejecuta: php artisan config:clear\n\n";

echo "2. Si aparece 'Forbidden' (403):\n";
echo "   • Verifica que tu usuario tiene el módulo 'materias'\n";
echo "   • Verifica que tu rol tiene permisos correctos\n\n";

echo "3. Si los botones no funcionan:\n";
echo "   • Verifica la consola del navegador (F12)\n";
echo "   • Verifica que las rutas se están generando correctamente\n";
echo "   • Revisa storage/logs/laravel.log para errores\n\n";

echo "═══════════════════════════════════════════════════════════\n";
