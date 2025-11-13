<?php
/**
 * Script de diagnóstico para exportación de dashboard
 * Base de datos: PostgreSQL
 *
 * Ejecutar: php diagnostico_exportacion.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Semestre;
use App\Models\Horario;
use App\Models\Grupo;
use App\Models\Docente;
use App\Models\Materia;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "   DIAGNÓSTICO DE EXPORTACIÓN DASHBOARD - PostgreSQL\n";
echo "═══════════════════════════════════════════════════════════\n\n";

// 1. Verificar conexión a base de datos
echo "1. CONEXIÓN A BASE DE DATOS\n";
echo "───────────────────────────────────────────────────────────\n";
try {
    $driver = DB::connection()->getDriverName();
    $database = DB::connection()->getDatabaseName();
    echo "✅ Conexión exitosa\n";
    echo "   Driver: {$driver}\n";
    echo "   Base de datos: {$database}\n\n";
} catch (\Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "\n\n";
    exit(1);
}

// 2. Verificar semestre activo
echo "2. SEMESTRE ACTIVO\n";
echo "───────────────────────────────────────────────────────────\n";
try {
    $semestre = Semestre::where('estado', 'Activo')->first();
    if ($semestre) {
        echo "✅ Semestre activo encontrado\n";
        echo "   ID: {$semestre->id}\n";
        echo "   Nombre: {$semestre->nombre}\n";
        echo "   Estado: {$semestre->estado}\n\n";
    } else {
        echo "❌ No hay semestre activo\n";
        echo "   Sugerencia: Activar un semestre\n\n";

        // Listar semestres disponibles
        $semestres = Semestre::all();
        if ($semestres->count() > 0) {
            echo "   Semestres disponibles:\n";
            foreach ($semestres as $s) {
                echo "   - {$s->nombre} (Estado: {$s->estado})\n";
            }
        }
        echo "\n";
    }
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
}

// 3. Verificar horarios
echo "3. HORARIOS EN BASE DE DATOS\n";
echo "───────────────────────────────────────────────────────────\n";
try {
    $totalHorarios = Horario::count();
    echo "Total horarios: {$totalHorarios}\n";

    if (isset($semestre)) {
        $horariosDelSemestre = Horario::whereHas('grupo', function($q) use ($semestre) {
            $q->where('semestre_id', $semestre->id);
        })->count();
        echo "Horarios del semestre activo: {$horariosDelSemestre}\n";

        if ($horariosDelSemestre > 0) {
            echo "✅ Hay horarios para exportar\n\n";

            // Mostrar muestra de 3 horarios
            $muestra = Horario::with(['grupo.materia', 'grupo.docente.user', 'aula'])
                ->whereHas('grupo', function($q) use ($semestre) {
                    $q->where('semestre_id', $semestre->id);
                })
                ->limit(3)
                ->get();

            echo "   Muestra de horarios:\n";
            foreach ($muestra as $h) {
                echo "   - {$h->grupo->materia->sigla} | {$h->grupo->nombre} | ";
                echo "{$h->grupo->docente->user->name} | {$h->aula->nombre}\n";
            }
            echo "\n";
        } else {
            echo "⚠️  No hay horarios en el semestre activo\n\n";
        }
    }
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
}

// 4. Verificar clases de exportación
echo "4. CLASES DE EXPORTACIÓN\n";
echo "───────────────────────────────────────────────────────────\n";

$exportClasses = [
    'HorarioSemanalExport' => 'app/Exports/HorarioSemanalExport.php',
    'AsistenciaExport' => 'app/Exports/AsistenciaExport.php',
];

foreach ($exportClasses as $class => $path) {
    $fullPath = __DIR__ . '/' . $path;
    if (file_exists($fullPath)) {
        echo "✅ {$class}\n";
        echo "   Path: {$path}\n";
    } else {
        echo "❌ {$class} NO ENCONTRADO\n";
        echo "   Path esperado: {$path}\n";
    }
}
echo "\n";

// 5. Verificar vistas PDF
echo "5. VISTAS PDF\n";
echo "───────────────────────────────────────────────────────────\n";

$pdfViews = [
    'horario_semanal' => 'resources/views/pdf/horario_semanal.blade.php',
    'asistencia' => 'resources/views/pdf/asistencia.blade.php',
];

foreach ($pdfViews as $view => $path) {
    $fullPath = __DIR__ . '/' . $path;
    if (file_exists($fullPath)) {
        echo "✅ pdf.{$view}\n";
        echo "   Path: {$path}\n";
    } else {
        echo "❌ pdf.{$view} NO ENCONTRADO\n";
        echo "   Path esperado: {$path}\n";
    }
}
echo "\n";

// 6. Verificar extensiones/paquetes
echo "6. PAQUETES NECESARIOS\n";
echo "───────────────────────────────────────────────────────────\n";

try {
    // Verificar Maatwebsite Excel
    if (class_exists('Maatwebsite\Excel\Facades\Excel')) {
        echo "✅ Maatwebsite/Excel instalado\n";
    } else {
        echo "❌ Maatwebsite/Excel NO instalado\n";
        echo "   Ejecutar: composer require maatwebsite/excel\n";
    }

    // Verificar DomPDF
    if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
        echo "✅ DomPDF instalado\n";
    } else {
        echo "❌ DomPDF NO instalado\n";
        echo "   Ejecutar: composer require barryvdh/laravel-dompdf\n";
    }
} catch (\Exception $e) {
    echo "❌ Error verificando paquetes: " . $e->getMessage() . "\n";
}
echo "\n";

// 7. Probar query de exportación
echo "7. PRUEBA DE QUERY EXPORTACIÓN\n";
echo "───────────────────────────────────────────────────────────\n";

if (isset($semestre)) {
    try {
        echo "Probando query con filtro docente_id = 38...\n";

        $query = Horario::query()
            ->whereHas('grupo', function ($query) use ($semestre) {
                $query->where('semestre_id', $semestre->id);
            })
            ->with(['grupo.materia', 'grupo.docente.user', 'aula']);

        // Aplicar filtro de docente
        $query->whereHas('grupo', function ($q) {
            $q->where('docente_id', 38);
        });

        $resultados = $query->get();
        $count = $resultados->count();

        if ($count > 0) {
            echo "✅ Query exitoso: {$count} resultados\n";
            echo "   Primer resultado:\n";
            $h = $resultados->first();
            echo "   - Materia: {$h->grupo->materia->nombre}\n";
            echo "   - Grupo: {$h->grupo->nombre}\n";
            echo "   - Docente: {$h->grupo->docente->user->name}\n";
            echo "   - Aula: {$h->aula->nombre}\n";
        } else {
            echo "⚠️  Query exitoso pero sin resultados\n";
            echo "   No hay horarios para docente_id = 38\n";
        }

    } catch (\Exception $e) {
        echo "❌ Error en query: " . $e->getMessage() . "\n";
        echo "   SQL: " . $e->getSql() ?? 'N/A' . "\n";
    }
} else {
    echo "⚠️  No se puede probar (no hay semestre activo)\n";
}
echo "\n";

// 8. Verificar controlador
echo "8. MÉTODOS DEL CONTROLADOR\n";
echo "───────────────────────────────────────────────────────────\n";

$controller = 'app/Http/Controllers/DashboardController.php';
$controllerPath = __DIR__ . '/' . $controller;

if (file_exists($controllerPath)) {
    echo "✅ DashboardController existe\n";

    $content = file_get_contents($controllerPath);

    $methods = [
        'exportHorarioSemanal',
        'exportHorarioSemanalPdf',
        'exportAsistencia',
        'exportAsistenciaPdf',
    ];

    foreach ($methods as $method) {
        if (strpos($content, "function {$method}") !== false) {
            echo "   ✅ {$method}()\n";
        } else {
            echo "   ❌ {$method}() NO ENCONTRADO\n";
        }
    }
} else {
    echo "❌ DashboardController NO ENCONTRADO\n";
}
echo "\n";

// 9. Verificar JavaScript
echo "9. FUNCIONES JAVASCRIPT\n";
echo "───────────────────────────────────────────────────────────\n";

$layoutPath = __DIR__ . '/resources/views/layouts/app.blade.php';

if (file_exists($layoutPath)) {
    echo "✅ app.blade.php existe\n";

    $content = file_get_contents($layoutPath);

    $functions = [
        'submitExportForm',
        'exportPdfWithFilters',
    ];

    foreach ($functions as $func) {
        if (strpos($content, "function {$func}") !== false) {
            echo "   ✅ {$func}()\n";
        } else {
            echo "   ❌ {$func}() NO ENCONTRADA\n";
        }
    }
} else {
    echo "❌ app.blade.php NO ENCONTRADO\n";
}
echo "\n";

// 10. Resumen y recomendaciones
echo "═══════════════════════════════════════════════════════════\n";
echo "   RESUMEN Y RECOMENDACIONES\n";
echo "═══════════════════════════════════════════════════════════\n\n";

if (!isset($semestre)) {
    echo "❌ PROBLEMA CRÍTICO: No hay semestre activo\n";
    echo "   Solución:\n";
    echo "   php artisan tinker\n";
    echo "   >>> \\App\\Models\\Semestre::first()->update(['estado' => 'Activo'])\n\n";
}

echo "Para probar exportación manualmente:\n";
echo "1. Acceder a: http://127.0.0.1:8000/dashboard?tab=horarios\n";
echo "2. Abrir consola del navegador (F12)\n";
echo "3. Verificar errores JavaScript\n";
echo "4. Intentar exportar y revisar Network tab\n\n";

echo "Para ver logs:\n";
echo "Get-Content storage/logs/laravel.log -Tail 50\n\n";

echo "═══════════════════════════════════════════════════════════\n";
echo "   DIAGNÓSTICO COMPLETADO\n";
echo "═══════════════════════════════════════════════════════════\n\n";
