<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencia Registrada</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full">
            @if($tipo === 'success')
                {{-- Éxito --}}
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-20 h-20 mb-4 bg-green-100 rounded-full">
                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">¡Asistencia Registrada!</h2>
                    <p class="text-sm text-gray-600">{{ $mensaje }}</p>
                </div>

                {{-- Detalles --}}
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-700 mb-3">Detalles del Registro:</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Materia:</span>
                            <span class="font-medium text-gray-900">{{ $horario->grupo->materia->sigla }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Grupo:</span>
                            <span class="font-medium text-gray-900">{{ $horario->grupo->nombre }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Aula:</span>
                            <span class="font-medium text-gray-900">{{ $horario->aula->nombre }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Fecha:</span>
                            <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Hora:</span>
                            <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($hora)->format('H:i:s') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Método:</span>
                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5z" clip-rule="evenodd" />
                                </svg>
                                Código QR
                            </span>
                        </div>
                    </div>
                </div>
            @else
                {{-- Info (ya registrado) --}}
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-20 h-20 mb-4 bg-blue-100 rounded-full">
                        <svg class="w-12 h-12 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Asistencia ya Registrada</h2>
                    <p class="text-sm text-gray-600">{{ $mensaje }}</p>
                </div>

                {{-- Detalles simplificados --}}
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Materia:</span>
                            <span class="font-medium text-gray-900">{{ $horario->grupo->materia->sigla }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Grupo:</span>
                            <span class="font-medium text-gray-900">{{ $horario->grupo->nombre }}</span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Botón de acción --}}
            <a href="{{ route('dashboard') }}" class="block w-full px-4 py-3 text-center text-sm font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition-colors">
                Ir al Dashboard
            </a>

            <p class="mt-4 text-xs text-center text-gray-500">
                Puedes cerrar esta ventana de forma segura
            </p>
        </div>
    </div>
</body>
</html>
