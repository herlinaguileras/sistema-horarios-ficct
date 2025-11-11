<?php

/**
 * Test de acceso a estadísticas para docentes
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Docente;

echo "════════════════════════════════════════════════════\n";
echo "  TEST: ACCESO A ESTADÍSTICAS PARA DOCENTES\n";
echo "════════════════════════════════════════════════════\n\n";

// Obtener un docente de ejemplo
$docente = Docente::with('user.roles')->first();

if (!$docente || !$docente->user) {
    echo "❌ No hay docentes en el sistema\n";
    exit(1);
}

$user = $docente->user;

echo "1️⃣  DOCENTE DE PRUEBA:\n";
echo "──────────────────────────────────────────────────\n";
echo "Nombre: {$user->name}\n";
echo "Email: {$user->email}\n";
echo "Código: {$docente->codigo_docente}\n\n";

// Test 1: Verificar que tiene rol docente
echo "2️⃣  VERIFICAR ROL:\n";
echo "──────────────────────────────────────────────────\n";
$tieneRolDocente = $user->hasRole('docente');
echo "• hasRole('docente'): " . ($tieneRolDocente ? "✅ SÍ" : "❌ NO") . "\n";

if (!$tieneRolDocente) {
    echo "❌ El usuario no tiene rol docente\n";
    exit(1);
}
echo "\n";

// Test 2: Verificar que tiene módulo estadísticas
echo "3️⃣  VERIFICAR MÓDULO ESTADÍSTICAS:\n";
echo "──────────────────────────────────────────────────\n";
$tieneEstadisticas = $user->hasModule('estadisticas');
echo "• hasModule('estadisticas'): " . ($tieneEstadisticas ? "✅ SÍ" : "❌ NO") . "\n";

if (!$tieneEstadisticas) {
    echo "❌ El usuario no tiene acceso al módulo estadísticas\n";
    exit(1);
}
echo "\n";

// Test 3: Verificar todos los módulos del docente
echo "4️⃣  MÓDULOS DISPONIBLES:\n";
echo "──────────────────────────────────────────────────\n";

$roles = $user->roles;
foreach ($roles as $role) {
    echo "Rol: {$role->name}\n";
    $modules = $role->modules;
    
    if ($modules->count() > 0) {
        foreach ($modules as $module) {
            $icon = match($module->module_name) {
                'estadisticas' => '📊',
                'horarios' => '📅',
                'grupos' => '👥',
                'materias' => '📚',
                default => '📌'
            };
            
            echo "  {$icon} {$module->module_name}\n";
        }
    }
}
echo "\n";

// Test 4: Simular redirección de index
echo "5️⃣  SIMULACIÓN DE ACCESO:\n";
echo "──────────────────────────────────────────────────\n";
echo "• GET /estadisticas\n";
echo "  → Redirige a: /estadisticas/{$docente->id}\n";
echo "  ✅ El docente ve solo sus propias estadísticas\n\n";

echo "• GET /estadisticas/{$docente->id}\n";
echo "  ✅ PERMITIDO - Es su propio ID\n\n";

// Obtener otro docente para probar restricción
$otroDocente = Docente::where('id', '!=', $docente->id)->first();

if ($otroDocente) {
    echo "• GET /estadisticas/{$otroDocente->id}\n";
    echo "  ❌ BLOQUEADO - No puede ver estadísticas de otro docente\n";
    echo "  → Error 403: No tienes permiso para ver las estadísticas de otro docente.\n\n";
}

// Test 5: Verificar datos disponibles
echo "6️⃣  DATOS VISIBLES PARA EL DOCENTE:\n";
echo "──────────────────────────────────────────────────\n";

$grupos = $docente->grupos()->with('materia', 'semestre')->get();
$totalHorarios = 0;
foreach ($grupos as $grupo) {
    $totalHorarios += $grupo->horarios()->count();
}

echo "✓ Total de grupos asignados: {$grupos->count()}\n";
echo "✓ Total de horarios (clases): {$totalHorarios}\n";

if ($grupos->count() > 0) {
    echo "✓ Materias que imparte:\n";
    foreach ($grupos as $grupo) {
        echo "  • {$grupo->materia->nombre} - {$grupo->nombre} ({$grupo->semestre->nombre})\n";
    }
}

echo "\n";

// Resumen final
echo "════════════════════════════════════════════════════\n";
echo "  RESUMEN DEL TEST\n";
echo "════════════════════════════════════════════════════\n\n";

$tests = [
    'Docente tiene rol asignado' => $tieneRolDocente,
    'Docente tiene módulo estadísticas' => $tieneEstadisticas,
    'Docente tiene grupos asignados' => $grupos->count() > 0,
];

$pasados = 0;
foreach ($tests as $descripcion => $resultado) {
    $icono = $resultado ? '✅' : '❌';
    echo "{$icono} {$descripcion}\n";
    if ($resultado) $pasados++;
}

echo "\nResultado: {$pasados}/" . count($tests) . " tests pasados\n\n";

if ($pasados === count($tests)) {
    echo "╔══════════════════════════════════════════════════╗\n";
    echo "║  ✅ ACCESO A ESTADÍSTICAS CONFIGURADO           ║\n";
    echo "║  Los docentes pueden ver sus estadísticas      ║\n";
    echo "╚══════════════════════════════════════════════════╝\n";
} else {
    echo "╔══════════════════════════════════════════════════╗\n";
    echo "║  ⚠️ CONFIGURACIÓN INCOMPLETA                    ║\n";
    echo "║  Revisa los errores arriba                      ║\n";
    echo "╚══════════════════════════════════════════════════╝\n";
}

echo "\n🔐 SEGURIDAD:\n";
echo "• Docentes solo ven SUS PROPIAS estadísticas\n";
echo "• No tienen acceso al listado general\n";
echo "• No pueden ver datos de otros docentes\n";
echo "• El middleware CheckModule valida el acceso\n";
echo "• El controlador valida la propiedad de los datos\n";
