<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Registrar Asistencia con QR') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes de éxito/error --}}
            @if(session('success'))
                <div class="p-4 mb-4 text-green-700 bg-green-100 border-l-4 border-green-500 rounded">
                    <p class="font-bold">✅ Éxito</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-4 text-red-700 bg-red-100 border-l-4 border-red-500 rounded">
                    <p class="font-bold">❌ Error</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @if(session('warning'))
                <div class="p-4 mb-4 text-yellow-700 bg-yellow-100 border-l-4 border-yellow-500 rounded">
                    <p class="font-bold">⚠️ Advertencia</p>
                    <p>{{ session('warning') }}</p>
                </div>
            @endif

            @if(session('info'))
                <div class="p-4 mb-4 text-blue-700 bg-blue-100 border-l-4 border-blue-500 rounded">
                    <p class="font-bold">ℹ️ Información</p>
                    <p>{{ session('info') }}</p>
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="grid gap-6 md:grid-cols-2">
                        {{-- Opciones de escaneo --}}
                        <div>
                            <h3 class="mb-4 text-lg font-semibold text-gray-800">📷 Escanear Código QR</h3>

                            <div class="space-y-4">
                                {{-- Opción 1: Usar cámara --}}
                                <div class="p-4 border border-blue-200 rounded-lg bg-blue-50">
                                    <h4 class="mb-2 font-semibold text-blue-900">Opción 1: Usar Cámara</h4>
                                    <button
                                        id="start-camera-btn"
                                        onclick="startCamera()"
                                        class="w-full px-4 py-3 font-bold text-white transition duration-150 bg-blue-500 rounded hover:bg-blue-600">
                                        📹 Activar Cámara
                                    </button>

                                    <div id="camera-container" class="hidden mt-4">
                                        <video id="qr-video" class="w-full border-2 border-blue-300 rounded"></video>
                                        <p class="mt-2 text-sm text-center text-blue-700">
                                            Apunta la cámara al código QR del docente
                                        </p>
                                        <button
                                            onclick="stopCamera()"
                                            class="w-full px-4 py-2 mt-2 font-bold text-white bg-red-500 rounded hover:bg-red-600">
                                            🛑 Detener Cámara
                                        </button>
                                    </div>
                                </div>

                                {{-- Opción 2: Pegar manualmente --}}
                                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                                    <h4 class="mb-2 font-semibold text-gray-900">Opción 2: Ingresar Manualmente</h4>
                                    <form method="POST" action="{{ route('asistencia.qr.process') }}">
                                        @csrf
                                        <textarea
                                            name="qr_data"
                                            id="qr_data"
                                            rows="4"
                                            placeholder='{"codigo_docente":"DOC001","timestamp":1234567890,"signature":"..."}'
                                            class="w-full font-mono text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required></textarea>

                                        <button
                                            type="submit"
                                            class="w-full px-4 py-2 mt-2 font-bold text-white transition duration-150 bg-green-500 rounded hover:bg-green-600">
                                            ✅ Registrar Asistencia
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Instrucciones --}}
                            <div class="p-4 mt-4 bg-gray-100 rounded-lg">
                                <h4 class="mb-2 font-semibold text-gray-800">📋 Instrucciones:</h4>
                                <ol class="space-y-1 text-sm text-gray-700 list-decimal list-inside">
                                    <li>Solicita al docente que muestre su código QR</li>
                                    <li>Usa la cámara o copia los datos del QR</li>
                                    <li>El sistema validará automáticamente el horario</li>
                                    <li>La asistencia se registrará si todo es correcto</li>
                                </ol>
                            </div>
                        </div>

                        {{-- Vista previa e información --}}
                        <div>
                            <h3 class="mb-4 text-lg font-semibold text-gray-800">👤 Información del Escaneo</h3>

                            <div id="docente-info" class="hidden p-4 mb-4 border-2 border-green-300 rounded-lg bg-green-50">
                                <h4 class="mb-2 font-semibold text-green-900">✅ QR Detectado</h4>
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <span class="font-medium text-gray-600">Docente:</span>
                                        <p class="text-gray-900" id="docente-nombre">-</p>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-600">Código:</span>
                                        <p class="font-mono text-gray-900" id="docente-codigo">-</p>
                                    </div>
                                </div>
                            </div>

                            <div id="scan-status" class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                                <p class="text-center text-gray-600">
                                    <svg class="inline-block w-6 h-6 mr-2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                    Esperando escaneo de QR...
                                </p>
                            </div>

                            {{-- Sección de clases del docente (se muestra después de escanear) --}}
                            <div id="clases-docente" class="hidden mt-6">
                                <h4 class="mb-3 font-semibold text-gray-800">📅 Clases de Hoy</h4>
                                <div id="lista-clases" class="space-y-3">
                                    {{-- Se llenará dinámicamente con JavaScript --}}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Botón volver --}}
                    <div class="flex justify-end mt-6">
                        <a href="{{ route('dashboard') }}"
                           class="px-6 py-2 font-bold text-gray-800 transition duration-150 bg-gray-300 rounded-lg hover:bg-gray-400">
                            ← Volver al Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts para escaneo QR con cámara --}}
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        let html5QrCode;

        function startCamera() {
            const cameraContainer = document.getElementById('camera-container');
            const startBtn = document.getElementById('start-camera-btn');

            cameraContainer.classList.remove('hidden');
            startBtn.classList.add('hidden');

            html5QrCode = new Html5Qrcode("qr-video");

            html5QrCode.start(
                { facingMode: "environment" }, // Usar cámara trasera en móviles
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                onScanSuccess,
                onScanFailure
            ).catch(err => {
                alert('Error al acceder a la cámara: ' + err);
                stopCamera();
            });
        }

        function stopCamera() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    const cameraContainer = document.getElementById('camera-container');
                    const startBtn = document.getElementById('start-camera-btn');

                    cameraContainer.classList.add('hidden');
                    startBtn.classList.remove('hidden');
                }).catch(err => {
                    console.error('Error al detener cámara:', err);
                });
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            console.log('QR escaneado:', decodedText);

            // Detener cámara
            stopCamera();

            // Validar QR y cargar clases
            cargarClasesDocente(decodedText);
        }

        function onScanFailure(error) {
            // Ignorar errores de escaneo continuo
        }

        // Cargar clases del docente después de escanear QR
        function cargarClasesDocente(qrData) {
            document.getElementById('scan-status').innerHTML =
                '<p class="text-center text-blue-600"><span class="inline-block animate-spin">⏳</span> Validando QR...</p>';

            fetch('{{ route("asistencia.qr.obtener-clases") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ qr_data: qrData })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar información del docente
                    document.getElementById('docente-info').classList.remove('hidden');
                    document.getElementById('docente-nombre').textContent = data.docente.nombre;
                    document.getElementById('docente-codigo').textContent = data.docente.codigo;
                    
                    document.getElementById('scan-status').innerHTML =
                        '<p class="font-semibold text-center text-green-600">✅ QR válido - Selecciona la clase</p>';

                    // Mostrar clases
                    mostrarClases(data.clases, data.docente.id);
                } else {
                    document.getElementById('scan-status').innerHTML =
                        '<p class="font-semibold text-center text-red-600">❌ ' + data.message + '</p>';
                }
            })
            .catch(error => {
                console.error('Error cargando clases:', error);
                document.getElementById('scan-status').innerHTML =
                    '<p class="font-semibold text-center text-red-600">❌ Error al cargar clases</p>';
            });
        }

        // Mostrar lista de clases del docente
        function mostrarClases(clases, docenteId) {
            const listaClases = document.getElementById('lista-clases');
            const contenedorClases = document.getElementById('clases-docente');
            
            listaClases.innerHTML = '';

            if (clases.length === 0) {
                listaClases.innerHTML = `
                    <div class="p-6 text-center bg-gray-100 rounded-lg">
                        <p class="text-gray-600">No tiene clases programadas para hoy</p>
                    </div>
                `;
            } else {
                clases.forEach(clase => {
                    const claseDiv = document.createElement('div');
                    claseDiv.className = `p-4 border-2 rounded-lg ${
                        clase.asistencia_registrada ? 'bg-gray-100 border-gray-300' :
                        clase.en_ventana ? 'bg-green-50 border-green-500 ring-2 ring-green-200' :
                        'bg-blue-50 border-blue-300'
                    }`;
                    
                    claseDiv.innerHTML = `
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    ${clase.asistencia_registrada ? 
                                        '<span class="px-2 py-1 text-xs font-bold text-white bg-gray-500 rounded-full">✅ Registrada</span>' :
                                        clase.en_ventana ? 
                                        '<span class="px-2 py-1 text-xs font-bold text-white bg-green-500 rounded-full animate-pulse">🔴 DISPONIBLE</span>' :
                                        '<span class="px-2 py-1 text-xs font-semibold text-white bg-blue-500 rounded-full">⏰ Próxima</span>'
                                    }
                                    ${clase.asistencia_registrada ? 
                                        `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">${clase.hora_registro}</span>` : 
                                        ''
                                    }
                                </div>
                                <h5 class="text-lg font-bold text-gray-900">${clase.materia}</h5>
                                <p class="text-sm text-gray-600">${clase.sigla} - Grupo ${clase.grupo}</p>
                                <div class="mt-2 space-y-1 text-sm text-gray-700">
                                    <p>⏰ ${clase.hora_inicio} - ${clase.hora_fin}</p>
                                    <p>🏫 Aula ${clase.aula}</p>
                                    <p class="text-xs text-gray-500">Ventana: ${clase.ventana_inicio} - ${clase.ventana_fin}</p>
                                </div>
                            </div>
                            <div class="ml-4">
                                ${clase.asistencia_registrada ? 
                                    '<button disabled class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-300 rounded-lg cursor-not-allowed">Ya Registrada</button>' :
                                    `<button onclick="marcarAsistencia(${docenteId}, ${clase.horario_id}, '${clase.materia}')" 
                                             class="${clase.en_ventana ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700'} 
                                                    text-white px-4 py-2 rounded-lg font-semibold text-sm transition duration-150 shadow-md hover:shadow-lg">
                                        ${clase.en_ventana ? '✓ Marcar AHORA' : 'Marcar Asistencia'}
                                    </button>`
                                }
                            </div>
                        </div>
                    `;
                    
                    listaClases.appendChild(claseDiv);
                });
            }

            contenedorClases.classList.remove('hidden');
        }

        // Marcar asistencia de una clase específica
        function marcarAsistencia(docenteId, horarioId, materiaName) {
            if (!confirm(`¿Confirmar asistencia para ${materiaName}?`)) {
                return;
            }

            const button = event.target;
            button.disabled = true;
            button.textContent = 'Registrando...';

            fetch('{{ url("/asistencia/qr/marcar-asistencia-directa") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    docente_id: docenteId,
                    horario_id: horarioId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    // Recargar la página para mostrar actualización
                    location.reload();
                } else {
                    alert('❌ ' + data.message);
                    button.disabled = false;
                    button.textContent = 'Marcar Asistencia';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al registrar asistencia');
                button.disabled = false;
                button.textContent = 'Marcar Asistencia';
            });
        }
    </script>
</x-app-layout>

