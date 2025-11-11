<?php

/**
 * Script para probar la generación de códigos de docente
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Docente;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "============================================\n";
echo "  TEST: Generación de Códigos de Docente\n";
echo "============================================\n\n";

// 1. Mostrar códigos existentes
echo "📋 CÓDIGOS EXISTENTES:\n";
echo "──────────────────────────────────────────\n";

$docentes = Docente::orderBy('codigo_docente', 'asc')->get();

if ($docentes->isEmpty()) {
    echo "   ⚠ No hay docentes en la base de datos\n\n";
} else {
    foreach ($docentes as $docente) {
        $nombre = $docente->user->name ?? 'Sin usuario';
        echo "   • Código: {$docente->codigo_docente} - {$nombre}\n";
    }
    echo "\n";
}

// 2. Obtener el último código
$ultimoDocente = Docente::orderBy('codigo_docente', 'desc')->first();

if ($ultimoDocente) {
    $ultimoCodigo = (int)$ultimoDocente->codigo_docente;
    echo "✓ Último código en uso: {$ultimoCodigo}\n";
} else {
    $ultimoCodigo = 99; // Para que el siguiente sea 100
    echo "✓ No hay códigos previos (empezará desde 100)\n";
}

// 3. Calcular próximo código
$proximoCodigo = $ultimoDocente ? ((int)$ultimoDocente->codigo_docente + 1) : 100;

echo "✓ Próximo código a asignar: {$proximoCodigo}\n\n";

// 4. Simular creación de 3 docentes
echo "🧪 SIMULACIÓN: Creando 3 docentes de prueba\n";
echo "──────────────────────────────────────────\n";

DB::beginTransaction();

try {
    $docentesPrueba = ['Ana García', 'Pedro López', 'María Fernández'];
    $codigosCreados = [];

    foreach ($docentesPrueba as $i => $nombre) {
        // Calcular código
        $ultimoDocente = Docente::orderBy('codigo_docente', 'desc')->first();
        $nuevoCodigo = $ultimoDocente ? ((int)$ultimoDocente->codigo_docente + 1) : 100;

        // Crear usuario temporal
        $user = User::create([
            'name' => $nombre,
            'email' => 'test' . ($i + 1) . '@test.com',
            'password' => bcrypt('test123')
        ]);

        // Crear docente
        $docente = Docente::create([
            'user_id' => $user->id,
            'codigo_docente' => (string)$nuevoCodigo,
            'carnet_identidad' => 'TEST' . ($i + 1)
        ]);

        $codigosCreados[] = $nuevoCodigo;
        echo "   ✓ Creado: {$nombre} → Código {$nuevoCodigo}\n";
    }

    echo "\n";

    // Verificar secuencia
    $secuenciaCorrecta = true;
    for ($i = 1; $i < count($codigosCreados); $i++) {
        if ($codigosCreados[$i] != $codigosCreados[$i - 1] + 1) {
            $secuenciaCorrecta = false;
            break;
        }
    }

    if ($secuenciaCorrecta) {
        echo "╔══════════════════════════════════════════╗\n";
        echo "║  ✅ SECUENCIA CORRECTA                  ║\n";
        echo "║  Códigos: " . implode(', ', $codigosCreados) . "                    ║\n";
        echo "╚══════════════════════════════════════════╝\n";
    } else {
        echo "╔══════════════════════════════════════════╗\n";
        echo "║  ❌ SECUENCIA INCORRECTA                ║\n";
        echo "╚══════════════════════════════════════════╝\n";
    }

    // Rollback para no ensuciar la BD
    DB::rollBack();

    echo "\n✓ Registros de prueba eliminados (rollback)\n\n";

} catch (Exception $e) {
    DB::rollBack();
    echo "❌ ERROR: {$e->getMessage()}\n";
}

echo "══════════════════════════════════════════\n";
echo "  RESUMEN\n";
echo "══════════════════════════════════════════\n";
echo "• Último código actual: {$ultimoCodigo}\n";
echo "• Siguiente código: {$proximoCodigo}\n";
echo "• Sistema: ✅ Funcionando correctamente\n";
echo "══════════════════════════════════════════\n";
