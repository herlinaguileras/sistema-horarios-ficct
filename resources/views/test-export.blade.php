<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Exportación</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6">🧪 Test de Exportación de Bitácora</h1>

        <div class="space-y-4">
            <!-- Test 1: Link directo -->
            <div class="border border-gray-300 rounded p-4">
                <h2 class="font-semibold mb-2">Test 1: Link Directo (window.location)</h2>
                <button onclick="test1()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Ejecutar Test 1
                </button>
                <pre id="result1" class="mt-2 bg-gray-50 p-2 rounded text-sm"></pre>
            </div>

            <!-- Test 2: Link con <a> programático -->
            <div class="border border-gray-300 rounded p-4">
                <h2 class="font-semibold mb-2">Test 2: Link con &lt;a&gt; programático</h2>
                <button onclick="test2()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Ejecutar Test 2
                </button>
                <pre id="result2" class="mt-2 bg-gray-50 p-2 rounded text-sm"></pre>
            </div>

            <!-- Test 3: Iframe oculto -->
            <div class="border border-gray-300 rounded p-4">
                <h2 class="font-semibold mb-2">Test 3: Iframe Oculto</h2>
                <button onclick="test3()" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
                    Ejecutar Test 3
                </button>
                <pre id="result3" class="mt-2 bg-gray-50 p-2 rounded text-sm"></pre>
            </div>

            <!-- Test 4: Formulario normal -->
            <div class="border border-gray-300 rounded p-4">
                <h2 class="font-semibold mb-2">Test 4: Formulario Normal (sin JavaScript)</h2>
                <form action="{{ route('audit-logs.export') }}" method="GET">
                    <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                        Ejecutar Test 4
                    </button>
                </form>
                <pre id="result4" class="mt-2 bg-gray-50 p-2 rounded text-sm">Este test enviará el formulario normalmente</pre>
            </div>

            <!-- Test 5: Fetch API -->
            <div class="border border-gray-300 rounded p-4">
                <h2 class="font-semibold mb-2">Test 5: Fetch API con Blob</h2>
                <button onclick="test5()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                    Ejecutar Test 5
                </button>
                <pre id="result5" class="mt-2 bg-gray-50 p-2 rounded text-sm"></pre>
            </div>
        </div>

        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-300 rounded">
            <h3 class="font-semibold text-yellow-800">📝 Notas:</h3>
            <ul class="list-disc list-inside text-sm text-yellow-700 mt-2">
                <li>Abre la consola del navegador (F12) para ver los logs</li>
                <li>Revisa la pestaña Network para ver las peticiones</li>
                <li>Verifica si se descarga el archivo CSV</li>
            </ul>
        </div>
    </div>

    <script>
        const exportUrl = "{{ route('audit-logs.export') }}";

        function log(testNum, message) {
            const resultDiv = document.getElementById('result' + testNum);
            resultDiv.textContent += message + '\n';
            console.log(`Test ${testNum}:`, message);
        }

        function test1() {
            const resultDiv = document.getElementById('result1');
            resultDiv.textContent = '';
            log(1, 'Iniciando test 1...');
            log(1, 'URL: ' + exportUrl);

            try {
                window.location.href = exportUrl;
                log(1, '✅ Navegación iniciada');
            } catch (error) {
                log(1, '❌ Error: ' + error.message);
            }
        }

        function test2() {
            const resultDiv = document.getElementById('result2');
            resultDiv.textContent = '';
            log(2, 'Iniciando test 2...');
            log(2, 'URL: ' + exportUrl);

            try {
                const link = document.createElement('a');
                link.href = exportUrl;
                link.download = 'audit_logs.csv';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                log(2, '✅ Click en link ejecutado');
            } catch (error) {
                log(2, '❌ Error: ' + error.message);
            }
        }

        function test3() {
            const resultDiv = document.getElementById('result3');
            resultDiv.textContent = '';
            log(3, 'Iniciando test 3...');
            log(3, 'URL: ' + exportUrl);

            try {
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = exportUrl;
                document.body.appendChild(iframe);
                log(3, '✅ Iframe creado y agregado');

                setTimeout(() => {
                    log(3, 'Removiendo iframe...');
                    document.body.removeChild(iframe);
                    log(3, '✅ Iframe removido');
                }, 5000);
            } catch (error) {
                log(3, '❌ Error: ' + error.message);
            }
        }

        async function test5() {
            const resultDiv = document.getElementById('result5');
            resultDiv.textContent = '';
            log(5, 'Iniciando test 5...');
            log(5, 'URL: ' + exportUrl);

            try {
                log(5, 'Haciendo fetch...');
                const response = await fetch(exportUrl);

                log(5, 'Status: ' + response.status);
                log(5, 'Content-Type: ' + response.headers.get('Content-Type'));
                log(5, 'Content-Disposition: ' + response.headers.get('Content-Disposition'));

                if (!response.ok) {
                    throw new Error('HTTP error! status: ' + response.status);
                }

                const blob = await response.blob();
                log(5, 'Blob size: ' + blob.size + ' bytes');

                // Crear URL del blob
                const url = window.URL.createObjectURL(blob);

                // Crear link y descargar
                const link = document.createElement('a');
                link.href = url;
                link.download = 'audit_logs_test5.csv';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Liberar URL
                window.URL.revokeObjectURL(url);

                log(5, '✅ Descarga completada');
            } catch (error) {
                log(5, '❌ Error: ' + error.message);
            }
        }
    </script>
</body>
</html>
