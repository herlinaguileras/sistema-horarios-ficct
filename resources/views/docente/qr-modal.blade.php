<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código QR - Asistencia</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full">
            {{-- Encabezado --}}
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 mb-4 bg-green-100 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM13 3a1 1 0 00-1 1v3a1 1 0 001 1h3a1 1 0 001-1V4a1 1 0 00-1-1h-3zm1 2v1h1V5h-1z" clip-rule="evenodd" />
                        <path d="M11 4a1 1 0 10-2 0v1a1 1 0 002 0V4zM10 7a1 1 0 011 1v1h2a1 1 0 110 2h-3a1 1 0 01-1-1V8a1 1 0 011-1zM16 9a1 1 0 100 2 1 1 0 000-2zM9 13a1 1 0 011-1h1a1 1 0 110 2v2a1 1 0 11-2 0v-3zM7 11a1 1 0 100-2H4a1 1 0 100 2h3zM17 13a1 1 0 01-1 1h-2a1 1 0 110-2h2a1 1 0 011 1zM16 17a1 1 0 100-2h-3a1 1 0 100 2h3z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Código QR para Asistencia</h2>
                <p class="text-sm text-gray-600">Escanea este código con tu celular para marcar asistencia</p>
            </div>

            {{-- Información de la clase --}}
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="font-semibold text-gray-700">Materia:</span>
                        <span class="text-gray-900">{{ $horario->grupo->materia->sigla }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="font-semibold text-gray-700">Grupo:</span>
                        <span class="text-gray-900">{{ $horario->grupo->nombre }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="font-semibold text-gray-700">Aula:</span>
                        <span class="text-gray-900">{{ $horario->aula->nombre }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="font-semibold text-gray-700">Horario:</span>
                        <span class="text-gray-900">{{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}</span>
                    </div>
                </div>
            </div>

            {{-- Código QR --}}
            <div class="flex justify-center mb-6 bg-white p-4 rounded-lg border-2 border-gray-200">
                <div class="inline-block">
                    {!! $qrCode !!}
                </div>
            </div>

            {{-- Instrucciones --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Instrucciones:</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Abre la cámara de tu celular</li>
                            <li>Apunta hacia el código QR</li>
                            <li>Toca la notificación que aparece</li>
                            <li>Tu asistencia se marcará automáticamente</li>
                        </ol>
                    </div>
                </div>
            </div>

            {{-- Advertencia --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-xs text-yellow-800">
                        Este código QR expira en <strong>1 hora</strong> por seguridad.
                    </p>
                </div>
            </div>

            {{-- Botón cerrar --}}
            <button onclick="window.close()" class="w-full px-4 py-2 text-sm font-semibold text-white bg-gray-600 rounded-md hover:bg-gray-700 transition-colors">
                Cerrar Ventana
            </button>
        </div>
    </div>
</body>
</html>
