<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

echo "=== TEST DE EXPORTACIÓN DE AUDIT LOGS ===\n\n";

// Test 1: Verificar que existan logs
echo "1. Verificando que existan logs en la base de datos...\n";
$count = AuditLog::count();
echo "   Total de logs: $count\n";

if ($count === 0) {
    echo "   ⚠️  WARNING: No hay logs en la base de datos\n";
} else {
    echo "   ✅ OK: Hay logs disponibles\n";
}

// Test 2: Verificar que se puedan cargar logs con user
echo "\n2. Verificando que se puedan cargar logs con relación user...\n";
try {
    $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->limit(5)->get();
    echo "   ✅ OK: Se cargaron " . $logs->count() . " logs\n";

    if ($logs->count() > 0) {
        $firstLog = $logs->first();
        echo "   - Primer log ID: {$firstLog->id}\n";
        echo "   - Acción: {$firstLog->action}\n";
        echo "   - Usuario: " . ($firstLog->user?->name ?? 'N/A') . "\n";
    }
} catch (\Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
}

// Test 3: Simular generación de CSV
echo "\n3. Simulando generación de CSV...\n";
try {
    $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->limit(10)->get();

    $tempFile = tempnam(sys_get_temp_dir(), 'audit_test_');
    $file = fopen($tempFile, 'w');

    // BOM para UTF-8
    fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

    // Encabezados
    fputcsv($file, [
        'ID',
        'Fecha/Hora',
        'Usuario',
        'Email',
        'Acción',
        'Endpoint',
        'Método HTTP',
        'IP',
        'Modelo',
        'ID Modelo',
    ]);

    // Datos
    foreach ($logs as $log) {
        fputcsv($file, [
            $log->id,
            $log->created_at->format('Y-m-d H:i:s'),
            $log->user?->name ?? 'N/A',
            $log->user?->email ?? 'N/A',
            $log->action,
            $log->endpoint ?? 'N/A',
            $log->http_method ?? 'N/A',
            $log->ip_address ?? 'N/A',
            $log->model_type ? class_basename($log->model_type) : 'N/A',
            $log->model_id ?? 'N/A',
        ]);
    }

    fclose($file);

    $fileSize = filesize($tempFile);
    echo "   ✅ OK: CSV generado exitosamente\n";
    echo "   - Tamaño del archivo: $fileSize bytes\n";
    echo "   - Registros exportados: " . $logs->count() . "\n";
    echo "   - Ubicación temporal: $tempFile\n";

    // Mostrar las primeras líneas
    echo "\n   Primeras 5 líneas del CSV:\n";
    $content = file_get_contents($tempFile);
    $lines = explode("\n", $content);
    foreach (array_slice($lines, 0, 5) as $index => $line) {
        echo "   " . ($index + 1) . ": " . substr($line, 0, 100) . "\n";
    }

    unlink($tempFile);

} catch (\Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}

// Test 4: Verificar la ruta
echo "\n4. Verificando la ruta de exportación...\n";
try {
    $route = route('audit-logs.export');
    echo "   ✅ OK: Ruta generada correctamente\n";
    echo "   - URL: $route\n";
} catch (\Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
}

// Test 5: Verificar que el controlador existe
echo "\n5. Verificando que el controlador AuditLogController existe...\n";
if (class_exists('App\Http\Controllers\AuditLogController')) {
    echo "   ✅ OK: Controlador existe\n";

    // Verificar que el método export existe
    if (method_exists('App\Http\Controllers\AuditLogController', 'export')) {
        echo "   ✅ OK: Método export() existe\n";
    } else {
        echo "   ❌ ERROR: Método export() no existe\n";
    }
} else {
    echo "   ❌ ERROR: Controlador no existe\n";
}

echo "\n=== FIN DEL TEST ===\n";
