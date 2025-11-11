<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n╔════════════════════════════════════════════════╗\n";
echo "║   CORRECCIÓN: Estados de Asistencia            ║\n";
echo "╚════════════════════════════════════════════════╝\n\n";

// 1. Verificar estados actuales
echo "🔍 Verificando estados actuales...\n";
$invalidos = DB::table('asistencias')
    ->whereNotIn('estado', ['presente', 'ausente', 'justificado', 'tardanza'])
    ->get();

if ($invalidos->isEmpty()) {
    echo "✓ No hay estados inválidos para corregir.\n\n";
    exit(0);
}

echo "Encontrados: " . $invalidos->count() . " registros con estados inválidos\n\n";

foreach ($invalidos as $asist) {
    echo "  • ID: {$asist->id} - Estado: '{$asist->estado}' → '" . strtolower($asist->estado) . "'\n";
}

// 2. Corrección
echo "\n🔧 Aplicando corrección...\n";

$updated = DB::table('asistencias')
    ->whereNotIn('estado', ['presente', 'ausente', 'justificado', 'tardanza'])
    ->update([
        'estado' => DB::raw('LOWER(estado)')
    ]);

echo "✓ Actualizado $updated registros\n\n";

// 3. Verificar corrección
echo "🔍 Verificando corrección...\n";
$pendientes = DB::table('asistencias')
    ->whereNotIn('estado', ['presente', 'ausente', 'justificado', 'tardanza'])
    ->count();

if ($pendientes === 0) {
    echo "✅ ¡Corrección completada exitosamente!\n\n";
} else {
    echo "⚠️  Todavía hay $pendientes registros con problemas\n\n";
}

echo "📊 Estado final de asistencias:\n";
$estados = DB::table('asistencias')
    ->select('estado', DB::raw('count(*) as total'))
    ->groupBy('estado')
    ->get();

foreach ($estados as $estado) {
    echo "  • {$estado->estado}: {$estado->total}\n";
}

echo "\n";
