<?php

/**
 * Script para probar la inserción de materias
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Materia;

echo "============================================\n";
echo "  TEST: Inserción de Materia\n";
echo "============================================\n\n";

// Intentar crear una materia de prueba
try {
    $materia = Materia::create([
        'sigla' => 'TEST123',
        'nombre' => 'MATERIA DE PRUEBA',
        'nivel_semestre' => 1
    ]);

    echo "✅ MATERIA CREADA EXITOSAMENTE\n";
    echo "   ID: {$materia->id}\n";
    echo "   Sigla: {$materia->sigla}\n";
    echo "   Nombre: {$materia->nombre}\n";
    echo "   Nivel Semestre: {$materia->nivel_semestre}\n\n";

    // Eliminar el registro de prueba
    $materia->delete();
    echo "✓ Registro de prueba eliminado\n\n";

    echo "╔══════════════════════════════════════════╗\n";
    echo "║  ✅ TEST PASADO - TODO FUNCIONA         ║\n";
    echo "╚══════════════════════════════════════════╝\n";

} catch (Exception $e) {
    echo "❌ ERROR AL CREAR MATERIA:\n";
    echo "   {$e->getMessage()}\n\n";
    echo "╔══════════════════════════════════════════╗\n";
    echo "║  ❌ TEST FALLIDO                         ║\n";
    echo "╚══════════════════════════════════════════╝\n";
}
