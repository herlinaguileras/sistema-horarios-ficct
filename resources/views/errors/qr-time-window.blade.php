<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fuera de Horario</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 mb-4 bg-orange-100 rounded-full">
                    <svg class="w-12 h-12 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Fuera de Horario</h2>
                <p class="text-gray-600 mb-4">{{ $error }}</p>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-800 mb-2">
                        <strong>Ventana permitida:</strong>
                    </p>
                    <p class="text-lg font-semibold text-blue-900">
                        {{ $ventana }}
                    </p>
                </div>

                <p class="text-sm text-gray-600 mb-6">
                    Solo puedes marcar asistencia dentro de la ventana de ±15 minutos desde el inicio de la clase.
                </p>

                <a href="{{ route('dashboard') }}" class="block w-full px-4 py-3 text-center text-sm font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition-colors">
                    Ir al Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>
