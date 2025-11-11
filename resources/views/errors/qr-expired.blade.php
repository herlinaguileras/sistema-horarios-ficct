<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código QR Expirado</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 mb-4 bg-red-100 rounded-full">
                    <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Código QR Expirado</h2>
                <p class="text-gray-600 mb-6">{{ $error }}</p>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-yellow-800">
                        Por seguridad, los códigos QR expiran después de 1 hora.
                        Por favor, genera un nuevo código desde tu cuenta.
                    </p>
                </div>

                <a href="{{ route('dashboard') }}" class="block w-full px-4 py-3 text-center text-sm font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition-colors">
                    Ir al Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>
