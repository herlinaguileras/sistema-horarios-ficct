<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST HTTP DE EXPORTACIÓN ===\n\n";

// Simular una petición GET a /audit-logs/export
echo "1. Simulando petición GET a /audit-logs/export...\n";

try {
    // Crear request
    $request = Illuminate\Http\Request::create(
        '/audit-logs/export',
        'GET',
        [] // parámetros
    );

    // Simular autenticación (necesario para pasar middleware)
    // Esto es un test básico, no simularemos autenticación completa

    // Procesar la request directamente con el controlador
    $controller = new App\Http\Controllers\AuditLogController();

    echo "2. Llamando al método export()...\n";
    $response = $controller->export($request);

    echo "3. Analizando respuesta...\n";

    // Verificar tipo de respuesta
    if ($response instanceof Symfony\Component\HttpFoundation\StreamedResponse) {
        echo "   ✅ OK: Respuesta es StreamedResponse (correcto para descarga)\n";

        // Capturar headers
        $headers = $response->headers->all();
        echo "   Headers:\n";
        foreach ($headers as $key => $value) {
            echo "   - $key: " . (is_array($value) ? implode(', ', $value) : $value) . "\n";
        }

        // Verificar Content-Type
        $contentType = $response->headers->get('Content-Type');
        if (strpos($contentType, 'text/csv') !== false) {
            echo "   ✅ OK: Content-Type es text/csv\n";
        } else {
            echo "   ⚠️  WARNING: Content-Type no es text/csv: $contentType\n";
        }

        // Verificar Content-Disposition
        $contentDisposition = $response->headers->get('Content-Disposition');
        if (strpos($contentDisposition, 'attachment') !== false) {
            echo "   ✅ OK: Content-Disposition indica descarga (attachment)\n";

            // Extraer nombre de archivo
            if (preg_match('/filename="([^"]+)"/', $contentDisposition, $matches)) {
                echo "   - Nombre de archivo: {$matches[1]}\n";
            }
        } else {
            echo "   ❌ ERROR: Content-Disposition no indica descarga\n";
        }

        // Capturar contenido
        echo "\n4. Capturando contenido del stream...\n";
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $size = strlen($content);
        echo "   ✅ OK: Contenido capturado\n";
        echo "   - Tamaño: $size bytes\n";

        if ($size > 0) {
            echo "   - Primeras 200 caracteres:\n";
            echo "   " . substr($content, 0, 200) . "\n";

            // Contar líneas
            $lines = explode("\n", $content);
            echo "   - Número de líneas: " . count($lines) . "\n";
        } else {
            echo "   ❌ ERROR: El contenido está vacío\n";
        }

    } else {
        echo "   ❌ ERROR: Respuesta no es StreamedResponse\n";
        echo "   Tipo de respuesta: " . get_class($response) . "\n";
    }

    echo "\n✅ TEST COMPLETADO EXITOSAMENTE\n";

} catch (\Exception $e) {
    echo "   ❌ ERROR durante el test:\n";
    echo "   Mensaje: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\n   Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DEL TEST HTTP ===\n";
