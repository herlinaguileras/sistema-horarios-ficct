<?php

/**
 * Test completo del sistema de roles para docentes
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Docente;

echo "============================================\n";
echo "  TEST COMPLETO: SISTEMA DE ROLES DOCENTE\n";
echo "============================================\n\n";

// 1. Verificar rol docente
echo "1️⃣  VERIFICAR ROL 'DOCENTE':\n";
echo "──────────────────────────────────────────\n";

$docenteRole = Role::where('name', 'docente')->first();

if (!$docenteRole) {
    echo "❌ ERROR: Rol 'docente' no existe\n";
    exit(1);
}

echo "✓ Rol encontrado: {$docenteRole->name}\n";
echo "  ID: {$docenteRole->id}\n";
echo "  Descripción: {$docenteRole->description}\n";
echo "  Módulos asignados: {$docenteRole->modules->count()}\n";

if ($docenteRole->modules->count() > 0) {
    echo "  Módulos:\n";
    foreach ($docenteRole->modules as $module) {
        echo "    • {$module->module_name}\n";
    }
}

echo "\n";

// 2. Verificar docentes
echo "2️⃣  VERIFICAR DOCENTES EN EL SISTEMA:\n";
echo "──────────────────────────────────────────\n";

$docentes = Docente::with('user.roles')->get();

echo "Total de docentes: {$docentes->count()}\n\n";

$docentesConRol = 0;
$docentesSinRol = 0;

foreach ($docentes as $docente) {
    if (!$docente->user) {
        echo "⚠️ Docente {$docente->codigo_docente}: Sin usuario\n";
        continue;
    }
    
    $tieneRolDocente = $docente->user->roles->contains('id', $docenteRole->id);
    
    if ($tieneRolDocente) {
        echo "✅ {$docente->user->name} ({$docente->codigo_docente})\n";
        echo "   Email: {$docente->user->email}\n";
        echo "   Roles: " . $docente->user->roles->pluck('name')->implode(', ') . "\n";
        $docentesConRol++;
    } else {
        echo "❌ {$docente->user->name} ({$docente->codigo_docente}): SIN ROL\n";
        $docentesSinRol++;
    }
}

echo "\n";

// 3. Test de acceso a módulos
echo "3️⃣  TEST DE ACCESO A MÓDULOS:\n";
echo "──────────────────────────────────────────\n";

$docenteEjemplo = Docente::with('user')->first();

if ($docenteEjemplo && $docenteEjemplo->user) {
    $user = $docenteEjemplo->user;
    
    echo "Docente de prueba: {$user->name}\n";
    echo "Email: {$user->email}\n\n";
    
    // Verificar método hasRole
    $tieneRolDocente = $user->roles->contains('name', 'docente');
    echo "• hasRole('docente'): " . ($tieneRolDocente ? "✅ TRUE" : "❌ FALSE") . "\n";
    
    // Verificar método hasModule (si existe)
    if (method_exists($user, 'hasModule')) {
        $tieneHorarios = $user->hasModule('horarios');
        $tieneGrupos = $user->hasModule('grupos');
        $tieneMaterias = $user->hasModule('materias');
        
        echo "• hasModule('horarios'): " . ($tieneHorarios ? "✅ TRUE" : "❌ FALSE") . "\n";
        echo "• hasModule('grupos'): " . ($tieneGrupos ? "✅ TRUE" : "❌ FALSE") . "\n";
        echo "• hasModule('materias'): " . ($tieneMaterias ? "✅ TRUE" : "❌ FALSE") . "\n";
    }
}

echo "\n";

// 4. Resumen final
echo "══════════════════════════════════════════\n";
echo "  RESUMEN DEL TEST\n";
echo "══════════════════════════════════════════\n\n";

$totalTests = 3;
$testsPasados = 0;

// Test 1: Rol docente existe
if ($docenteRole) {
    echo "✅ Test 1: Rol 'docente' existe\n";
    $testsPasados++;
} else {
    echo "❌ Test 1: Rol 'docente' NO existe\n";
}

// Test 2: Rol tiene módulos
if ($docenteRole->modules->count() > 0) {
    echo "✅ Test 2: Rol 'docente' tiene módulos asignados ({$docenteRole->modules->count()})\n";
    $testsPasados++;
} else {
    echo "❌ Test 2: Rol 'docente' SIN módulos\n";
}

// Test 3: Todos los docentes tienen rol
if ($docentesSinRol === 0 && $docentesConRol > 0) {
    echo "✅ Test 3: Todos los docentes tienen rol ({$docentesConRol}/{$docentes->count()})\n";
    $testsPasados++;
} else {
    echo "❌ Test 3: Hay docentes sin rol ({$docentesSinRol} sin rol)\n";
}

echo "\n";
echo "Resultado: {$testsPasados}/{$totalTests} tests pasados\n";
echo "══════════════════════════════════════════\n\n";

if ($testsPasados === $totalTests) {
    echo "╔══════════════════════════════════════════╗\n";
    echo "║  ✅ TODOS LOS TESTS PASARON             ║\n";
    echo "║  Sistema funcionando correctamente      ║\n";
    echo "╚══════════════════════════════════════════╝\n";
} else {
    echo "╔══════════════════════════════════════════╗\n";
    echo "║  ⚠️ ALGUNOS TESTS FALLARON              ║\n";
    echo "║  Revisa los errores arriba              ║\n";
    echo "╚══════════════════════════════════════════╝\n";
}
