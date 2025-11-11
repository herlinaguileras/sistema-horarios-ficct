<?php

/**
 * Script para asignar el rol "docente" a todos los usuarios docentes que no lo tengan
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Docente;

echo "============================================\n";
echo "  ASIGNAR ROL DOCENTE A USUARIOS\n";
echo "============================================\n\n";

// 1. Obtener el rol "docente"
$docenteRole = Role::where('name', 'docente')->first();

if (!$docenteRole) {
    echo "❌ ERROR: No se encontró el rol 'docente' en la base de datos\n";
    echo "   Por favor, crea el rol 'docente' primero.\n";
    exit(1);
}

echo "✓ Rol 'docente' encontrado (ID: {$docenteRole->id})\n\n";

// 2. Obtener todos los docentes
$docentes = Docente::with('user')->get();

echo "📊 Total de docentes en el sistema: {$docentes->count()}\n\n";

if ($docentes->isEmpty()) {
    echo "⚠️ No hay docentes registrados.\n";
    exit(0);
}

// 3. Verificar y asignar rol a cada docente
echo "──────────────────────────────────────────\n";
echo "  VERIFICACIÓN DE ROLES\n";
echo "──────────────────────────────────────────\n\n";

$docentesConRol = 0;
$docentesSinRol = 0;
$asignados = 0;

foreach ($docentes as $docente) {
    if (!$docente->user) {
        echo "⚠️ Docente {$docente->codigo_docente}: No tiene usuario asociado\n";
        continue;
    }
    
    $user = $docente->user;
    $tieneRolDocente = $user->roles()->where('roles.id', $docenteRole->id)->exists();
    
    if ($tieneRolDocente) {
        echo "✓ {$user->name} ({$docente->codigo_docente}): Ya tiene rol docente\n";
        $docentesConRol++;
    } else {
        echo "⚠️ {$user->name} ({$docente->codigo_docente}): Sin rol docente... ";
        
        // Asignar el rol
        $user->roles()->attach($docenteRole->id);
        
        echo "✅ ROL ASIGNADO\n";
        $docentesSinRol++;
        $asignados++;
    }
}

echo "\n";
echo "══════════════════════════════════════════\n";
echo "  RESUMEN\n";
echo "══════════════════════════════════════════\n";
echo "Total de docentes: {$docentes->count()}\n";
echo "├─ Con rol docente: {$docentesConRol}\n";
echo "├─ Sin rol docente: {$docentesSinRol}\n";
echo "└─ Roles asignados: {$asignados}\n";
echo "══════════════════════════════════════════\n\n";

if ($asignados > 0) {
    echo "╔══════════════════════════════════════════╗\n";
    echo "║  ✅ ROLES ASIGNADOS EXITOSAMENTE        ║\n";
    echo "║  Total: {$asignados} docente(s)                    ║\n";
    echo "╚══════════════════════════════════════════╝\n";
} else {
    echo "╔══════════════════════════════════════════╗\n";
    echo "║  ℹ️ TODOS LOS DOCENTES YA TIENEN ROL    ║\n";
    echo "╚══════════════════════════════════════════╝\n";
}

echo "\n💡 TIP: Los docentes ahora pueden iniciar sesión y ver sus módulos.\n";
