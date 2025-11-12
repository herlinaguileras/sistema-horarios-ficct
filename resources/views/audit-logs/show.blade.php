<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-file-alt text-blue-500"></i> {{ __('Detalle de Registro de Auditoría #') }}{{ $auditLog->id }}
            </h2>
            <a href="{{ route('audit-logs.index') }}"
               class="inline-flex items-center gap-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <!-- Breadcrumbs -->
            <nav class="text-sm text-gray-500 mb-6">
                <a href="{{ route('dashboard') }}" class="hover:text-gray-700">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <span class="mx-2">/</span>
                <a href="{{ route('audit-logs.index') }}" class="hover:text-gray-700">Bitácora</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900 font-semibold">Detalle #{{ $auditLog->id }}</span>
            </nav>

            <!-- Información del Usuario -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">
                        <i class="fas fa-user-circle text-blue-500"></i> Información del Usuario
                    </h3>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-blue-500 text-white flex items-center justify-center text-2xl font-bold shadow-lg">
                            {{ substr($auditLog->user?->name ?? 'S', 0, 1) }}
                        </div>
                        <div>
                            <div class="font-semibold text-lg text-gray-900">
                                {{ $auditLog->user?->name ?? 'Sistema Automático' }}
                            </div>
                            <div class="text-sm text-gray-500">
                                <i class="fas fa-envelope"></i> {{ $auditLog->user?->email ?? 'N/A' }}
                            </div>
                            @if($auditLog->user)
                                <div class="text-xs text-gray-400 mt-1">
                                    <i class="fas fa-id-badge"></i> ID Usuario: {{ $auditLog->user->id }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de la Acción -->
            <!-- Información de la Acción -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">
                        <i class="fas fa-bolt text-yellow-500"></i> Detalles de la Acción
                    </h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- ID -->
                        <div class="bg-gray-50 p-4 rounded">
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">
                                <i class="fas fa-hashtag"></i> ID del Registro
                            </dt>
                            <dd class="text-lg font-bold text-gray-900">{{ $auditLog->id }}</dd>
                        </div>

                        <!-- Fecha y Hora -->
                        <div class="bg-gray-50 p-4 rounded">
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">
                                <i class="fas fa-clock"></i> Fecha y Hora
                            </dt>
                            <dd class="text-sm font-semibold text-gray-900" title="{{ $auditLog->created_at->format('d/m/Y H:i:s') }}">
                                {{ $auditLog->created_at->diffForHumans() }}
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $auditLog->created_at->format('d/m/Y H:i:s') }}
                                </div>
                            </dd>
                        </div>

                        <!-- Acción -->
                        <div class="bg-gray-50 p-4 rounded">
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-2">
                                <i class="fas fa-tasks"></i> Tipo de Acción
                            </dt>
                            <dd>
                                <span class="px-4 py-2 inline-flex text-sm font-semibold rounded-full items-center gap-2
                                    @if(str_contains($auditLog->action, 'CREATE')) bg-green-100 text-green-800
                                    @elseif(str_contains($auditLog->action, 'UPDATE')) bg-blue-100 text-blue-800
                                    @elseif(str_contains($auditLog->action, 'DELETE')) bg-red-100 text-red-800
                                    @elseif(str_contains($auditLog->action, 'LOGIN')) bg-purple-100 text-purple-800
                                    @elseif(str_contains($auditLog->action, 'LOGOUT')) bg-orange-100 text-orange-800
                                    @elseif(str_contains($auditLog->action, 'IMPORT')) bg-yellow-100 text-yellow-800
                                    @elseif(str_contains($auditLog->action, 'EXPORT')) bg-indigo-100 text-indigo-800
                                    @else bg-gray-100 text-gray-800
                                    @endif
                                ">
                                    @if(str_contains($auditLog->action, 'CREATE'))
                                        <i class="fas fa-plus-circle"></i>
                                    @elseif(str_contains($auditLog->action, 'UPDATE'))
                                        <i class="fas fa-edit"></i>
                                    @elseif(str_contains($auditLog->action, 'DELETE'))
                                        <i class="fas fa-trash-alt"></i>
                                    @elseif(str_contains($auditLog->action, 'LOGIN'))
                                        <i class="fas fa-sign-in-alt"></i>
                                    @elseif(str_contains($auditLog->action, 'LOGOUT'))
                                        <i class="fas fa-sign-out-alt"></i>
                                    @elseif(str_contains($auditLog->action, 'IMPORT'))
                                        <i class="fas fa-file-import"></i>
                                    @elseif(str_contains($auditLog->action, 'EXPORT'))
                                        <i class="fas fa-file-export"></i>
                                    @else
                                        <i class="fas fa-info-circle"></i>
                                    @endif
                                    {{ $auditLog->action }}
                                </span>
                            </dd>
                        </div>

                        <!-- Método HTTP -->
                        <div class="bg-gray-50 p-4 rounded">
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-2">
                                <i class="fas fa-exchange-alt"></i> Método HTTP
                            </dt>
                            <dd>
                                <span class="px-4 py-2 text-sm font-bold rounded
                                    @if($auditLog->http_method == 'POST') bg-green-100 text-green-800
                                    @elseif($auditLog->http_method == 'GET') bg-blue-100 text-blue-800
                                    @elseif($auditLog->http_method == 'PUT' || $auditLog->http_method == 'PATCH') bg-yellow-100 text-yellow-800
                                    @elseif($auditLog->http_method == 'DELETE') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif
                                ">
                                    {{ $auditLog->http_method ?? 'N/A' }}
                                </span>
                            </dd>
                        </div>

                        <!-- Endpoint -->
                        <div class="bg-gray-50 p-4 rounded md:col-span-2">
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">
                                <i class="fas fa-link"></i> Endpoint / Ruta
                            </dt>
                            <dd class="text-sm">
                                <code class="bg-gray-800 text-green-400 px-3 py-2 rounded block">
                                    {{ $auditLog->endpoint ?? 'N/A' }}
                                </code>
                            </dd>
                        </div>

                        <!-- Modelo Afectado -->
                        @if($auditLog->model_type)
                            <div class="bg-gray-50 p-4 rounded md:col-span-2">
                                <dt class="text-xs font-medium text-gray-500 uppercase mb-1">
                                    <i class="fas fa-database"></i> Modelo Afectado
                                </dt>
                                <dd class="text-sm text-gray-900">
                                    <span class="font-semibold">{{ class_basename($auditLog->model_type) }}</span>
                                    @if($auditLog->model_id)
                                        <span class="text-gray-500 ml-2">
                                            <i class="fas fa-key text-xs"></i> ID: {{ $auditLog->model_id }}
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Información Técnica -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">
                        <i class="fas fa-server text-gray-500"></i> Información Técnica
                    </h3>
                    @php
                        $ua = $auditLog->user_agent ?? '';
                        $browser = 'Desconocido';
                        $os = 'Desconocido';

                        if (str_contains($ua, 'Chrome')) $browser = 'Google Chrome';
                        elseif (str_contains($ua, 'Firefox')) $browser = 'Mozilla Firefox';
                        elseif (str_contains($ua, 'Safari') && !str_contains($ua, 'Chrome')) $browser = 'Safari';
                        elseif (str_contains($ua, 'Edge')) $browser = 'Microsoft Edge';
                        elseif (str_contains($ua, 'Opera')) $browser = 'Opera';

                        if (str_contains($ua, 'Windows')) $os = 'Windows';
                        elseif (str_contains($ua, 'Mac')) $os = 'macOS';
                        elseif (str_contains($ua, 'Linux')) $os = 'Linux';
                        elseif (str_contains($ua, 'Android')) $os = 'Android';
                        elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) $os = 'iOS';
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded border-l-4 border-blue-500">
                            <div class="text-xs text-blue-600 font-medium mb-1">
                                <i class="fas fa-network-wired"></i> DIRECCIÓN IP
                            </div>
                            <div class="text-lg font-bold text-blue-900">
                                {{ $auditLog->ip_address }}
                            </div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded border-l-4 border-purple-500">
                            <div class="text-xs text-purple-600 font-medium mb-1">
                                <i class="fas fa-browser"></i> NAVEGADOR
                            </div>
                            <div class="text-lg font-bold text-purple-900">
                                {{ $browser }}
                            </div>
                        </div>
                        <div class="bg-green-50 p-4 rounded border-l-4 border-green-500">
                            <div class="text-xs text-green-600 font-medium mb-1">
                                <i class="fas fa-desktop"></i> SISTEMA OPERATIVO
                            </div>
                            <div class="text-lg font-bold text-green-900">
                                {{ $os }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 bg-gray-50 p-3 rounded">
                        <div class="text-xs text-gray-500 mb-1 font-medium">User Agent Completo:</div>
                        <div class="text-xs text-gray-700 break-all">{{ $auditLog->user_agent ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Detalles Adicionales (JSON) -->
            @if($auditLog->details)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-code text-indigo-500"></i> Detalles Adicionales (JSON)
                            </h3>
                            <button onclick="copyJSON()" class="bg-blue-500 text-white px-3 py-2 rounded text-sm hover:bg-blue-600 transition inline-flex items-center gap-2">
                                <i class="fas fa-copy"></i> Copiar JSON
                            </button>
                        </div>
                        <div class="relative">
                            <pre id="jsonContent" class="bg-gray-900 text-green-400 p-6 rounded-lg overflow-x-auto text-sm border-2 border-gray-700"><code>{{ json_encode(json_decode($auditLog->details), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
    function copyJSON() {
        const text = document.getElementById('jsonContent').textContent;
        navigator.clipboard.writeText(text).then(() => {
            // Mostrar notificación
            const button = event.target.closest('button');
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i> ¡Copiado!';
            button.classList.remove('bg-blue-500', 'hover:bg-blue-600');
            button.classList.add('bg-green-500');

            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('bg-green-500');
                button.classList.add('bg-blue-500', 'hover:bg-blue-600');
            }, 2000);
        });
    }
    </script>
</x-app-layout>
