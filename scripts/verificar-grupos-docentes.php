<?php

/**
 * Script para verificar y gestionar grupos asignados a docentes
 * Útil antes de eliminar docentes para prevenir errores de foreign key
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Semestre;

echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║  VERIFICACIÓN DE GRUPOS ASIGNADOS A DOCENTES             ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n\n";

// 1. Verificar todos los docentes y sus grupos
echo "📊 ANÁLISIS DE DOCENTES Y GRUPOS:\n";
echo "─────────────────────────────────────────────────────────────\n\n";

$docentes = Docente::with(['user', 'grupos.materia', 'grupos.semestre'])->get();
$totalDocentes = $docentes->count();
$docentesConGrupos = 0;
$docentesSinGrupos = 0;
$totalGrupos = 0;

$detallesPorDocente = [];

foreach ($docentes as $docente) {
    $gruposCount = $docente->grupos->count();
    $totalGrupos += $gruposCount;
    
    if ($gruposCount > 0) {
        $docentesConGrupos++;
        $detallesPorDocente[] = [
            'docente' => $docente,
            'grupos' => $docente->grupos
        ];
        
        echo "👨‍🏫 {$docente->user->name} (Código: {$docente->codigo_docente})\n";
        echo "   📚 {$gruposCount} grupo(s) asignado(s):\n";
        
        foreach ($docente->grupos as $grupo) {
            $semestre = $grupo->semestre->nombre ?? 'N/A';
            echo "   • {$grupo->materia->nombre} - Grupo {$grupo->nombre} (Semestre: {$semestre})\n";
        }
        echo "\n";
    } else {
        $docentesSinGrupos++;
    }
}

echo "─────────────────────────────────────────────────────────────\n\n";

// 2. Resumen estadístico
echo "📈 RESUMEN ESTADÍSTICO:\n";
echo "─────────────────────────────────────────────────────────────\n";
echo "Total de docentes: {$totalDocentes}\n";
echo "├─ Con grupos asignados: {$docentesConGrupos}\n";
echo "├─ Sin grupos asignados: {$docentesSinGrupos}\n";
echo "└─ Total de grupos: {$totalGrupos}\n\n";

// 3. Docentes que se pueden eliminar de forma segura
if ($docentesSinGrupos > 0) {
    echo "✅ DOCENTES QUE SE PUEDEN ELIMINAR DIRECTAMENTE:\n";
    echo "─────────────────────────────────────────────────────────────\n";
    
    $docentesEliminables = Docente::doesntHave('grupos')->with('user')->get();
    
    foreach ($docentesEliminables as $docente) {
        echo "• {$docente->user->name} (Código: {$docente->codigo_docente})\n";
        echo "  Email: {$docente->user->email}\n";
        echo "  ID: {$docente->id}\n\n";
    }
}

// 4. Advertencias sobre docentes con grupos
if ($docentesConGrupos > 0) {
    echo "⚠️  DOCENTES QUE REQUIEREN ACCIÓN PREVIA:\n";
    echo "─────────────────────────────────────────────────────────────\n";
    echo "Los siguientes docentes NO se pueden eliminar directamente:\n\n";
    
    foreach ($detallesPorDocente as $detalle) {
        $docente = $detalle['docente'];
        echo "❌ {$docente->user->name} (ID: {$docente->id})\n";
        echo "   Grupos asignados: {$detalle['grupos']->count()}\n";
        echo "   Acción requerida: Reasignar o eliminar grupos primero\n\n";
    }
}

echo "═══════════════════════════════════════════════════════════\n\n";

// 5. Opciones disponibles
echo "💡 OPCIONES DISPONIBLES:\n";
echo "─────────────────────────────────────────────────────────────\n\n";

echo "1️⃣  PARA ELIMINAR DOCENTES SIN GRUPOS:\n";
echo "   • Ve a: http://127.0.0.1:8000/docentes\n";
echo "   • Haz clic en 'Eliminar' del docente deseado\n";
echo "   • Confirma la acción\n\n";

echo "2️⃣  PARA DOCENTES CON GRUPOS - Opción A (Reasignar):\n";
echo "   • Ve a: http://127.0.0.1:8000/grupos\n";
echo "   • Edita cada grupo del docente\n";
echo "   • Asigna un nuevo docente\n";
echo "   • Luego podrás eliminar el docente original\n\n";

echo "3️⃣  PARA DOCENTES CON GRUPOS - Opción B (Eliminar grupos):\n";
echo "   • Ve a: http://127.0.0.1:8000/grupos\n";
echo "   • Elimina los grupos del docente\n";
echo "   • Luego podrás eliminar el docente\n\n";

echo "4️⃣  EJECUTAR LIMPIEZA AUTOMÁTICA (avanzado):\n";
echo "   • Ejecuta: php scripts/limpiar-grupos-docente.php [ID_DOCENTE]\n";
echo "   • Este script reasignará o eliminará grupos automáticamente\n\n";

echo "═══════════════════════════════════════════════════════════\n\n";

// 6. Generar recomendaciones específicas
if ($docentesConGrupos > 0) {
    echo "🎯 RECOMENDACIONES ESPECÍFICAS:\n";
    echo "─────────────────────────────────────────────────────────────\n\n";
    
    $semestreActivo = Semestre::where('estado', 'Activo')->first();
    
    foreach ($detallesPorDocente as $detalle) {
        $docente = $detalle['docente'];
        $grupos = $detalle['grupos'];
        
        $gruposActivos = $grupos->filter(function($grupo) use ($semestreActivo) {
            return $semestreActivo && $grupo->semestre_id === $semestreActivo->id;
        });
        
        $gruposPasados = $grupos->filter(function($grupo) use ($semestreActivo) {
            return !$semestreActivo || $grupo->semestre_id !== $semestreActivo->id;
        });
        
        echo "📌 {$docente->user->name}:\n";
        
        if ($gruposActivos->count() > 0) {
            echo "   ⚠️  Tiene {$gruposActivos->count()} grupo(s) en semestre ACTIVO\n";
            echo "   → REASIGNAR a otro docente (RECOMENDADO)\n";
        }
        
        if ($gruposPasados->count() > 0) {
            echo "   ℹ️  Tiene {$gruposPasados->count()} grupo(s) en semestres pasados\n";
            echo "   → Se pueden ELIMINAR de forma segura\n";
        }
        
        echo "\n";
    }
}

echo "═══════════════════════════════════════════════════════════\n";
echo "  VERIFICACIÓN COMPLETADA\n";
echo "═══════════════════════════════════════════════════════════\n";
