<?php

/**
 * Script para crear un semestre de prueba eliminable
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Semestre;

echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║  CREAR SEMESTRE DE PRUEBA                               ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

// Crear semestre de prueba
$semestre = Semestre::create([
    'nombre' => 'TEST - Semestre Eliminable',
    'fecha_inicio' => now()->addMonths(6),
    'fecha_fin' => now()->addMonths(10),
    'estado' => Semestre::ESTADO_PLANIFICACION,
]);

echo "✅ Semestre creado:\n";
echo "   ID: {$semestre->id}\n";
echo "   Nombre: {$semestre->nombre}\n";
echo "   Estado: {$semestre->estado}\n";
echo "   Grupos: " . $semestre->grupos()->count() . "\n\n";

echo "🎯 Este semestre PUEDE ser eliminado porque:\n";
echo "   ✅ NO es el semestre activo\n";
echo "   ✅ NO tiene grupos asociados\n";
echo "   ✅ Su estado es 'Planificación'\n\n";

echo "📝 INSTRUCCIONES:\n";
echo "1. Ve a: http://127.0.0.1:8000/semestres\n";
echo "2. Busca el semestre: 'TEST - Semestre Eliminable'\n";
echo "3. El botón 'Eliminar' debe estar en ROJO (habilitado)\n";
echo "4. Haz clic para eliminar\n";
echo "5. Confirma la acción\n";
echo "6. Debe eliminarse exitosamente\n\n";

echo "🧪 Para limpiar después de la prueba:\n";
echo "   php artisan tinker\n";
echo "   >>> Semestre::where('nombre', 'TEST - Semestre Eliminable')->delete();\n";
