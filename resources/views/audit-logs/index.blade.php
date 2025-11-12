<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Bitácora del Sistema') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('audit-logs.statistics') }}"
                   class="inline-flex items-center gap-2 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                    <i class="fas fa-chart-bar"></i> Estadísticas
                </a>
                <form action="{{ route('audit-logs.export') }}" method="GET" class="inline" onsubmit="showLoadingSpinner()">
                    @foreach(request()->except('_token') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <button type="submit" class="inline-flex items-center gap-2 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">
                        <i class="fas fa-file-csv"></i> Exportar CSV
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="fixed inset-0 bg-gray-900 bg-opacity-50 items-center justify-center z-50" style="display: none;">
        <div class="bg-white p-6 rounded-lg text-center shadow-2xl">
            <i class="fas fa-spinner fa-spin text-4xl text-blue-500 mb-4"></i>
            <p class="text-lg font-semibold">Generando exportación...</p>
            <p class="text-sm text-gray-500 mt-2">Por favor espere</p>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Contador de Resultados -->
            @if($logs->total() > 0)
                <div class="mb-4 text-sm text-gray-600 bg-white px-6 py-3 rounded-lg shadow-sm">
                    📊 Mostrando <strong>{{ $logs->firstItem() }}</strong> - <strong>{{ $logs->lastItem() }}</strong> de <strong>{{ $logs->total() }}</strong> registros
                </div>
            @endif

            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('audit-logs.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Filtro por Usuario -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                            <select name="user_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                <option value="">Todos los usuarios</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro por Acción -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Acción</label>
                            <select name="action" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                <option value="">Todas las acciones</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                        {{ $action }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro por IP -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dirección IP</label>
                            <input type="text" name="ip_address" value="{{ request('ip_address') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                   placeholder="Ej: 192.168.1.1">
                        </div>

                        <!-- Filtro por Fecha Inicio -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>

                        <!-- Filtro por Fecha Fin -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hasta</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>

                        <!-- Filtro por Endpoint -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Endpoint</label>
                            <input type="text" name="endpoint" value="{{ request('endpoint') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                   placeholder="Ej: docentes">
                        </div>

                        <div class="md:col-span-3 flex gap-2">
                            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 transition">
                                🔍 Filtrar
                            </button>
                            <a href="{{ route('audit-logs.index') }}"
                               class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 transition">
                                🔄 Limpiar Filtros
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Logs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Vista Desktop: Tabla -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha/Hora</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Endpoint</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($logs as $log)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="font-medium text-gray-700" title="{{ $log->created_at->format('d/m/Y H:i:s') }}">
                                                {{ $log->created_at->diffForHumans() }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $log->created_at->format('H:i:s') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="font-medium text-gray-900">
                                                {{ $log->user?->name ?? 'Usuario Eliminado' }}
                                            </div>
                                            <div class="text-gray-500 text-xs">
                                                {{ $log->user?->email ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full items-center gap-1
                                                @if(str_contains($log->action, 'CREATE')) bg-green-100 text-green-800
                                                @elseif(str_contains($log->action, 'UPDATE')) bg-blue-100 text-blue-800
                                                @elseif(str_contains($log->action, 'DELETE')) bg-red-100 text-red-800
                                                @elseif(str_contains($log->action, 'LOGIN')) bg-purple-100 text-purple-800
                                                @elseif(str_contains($log->action, 'LOGOUT')) bg-orange-100 text-orange-800
                                                @elseif(str_contains($log->action, 'IMPORT')) bg-yellow-100 text-yellow-800
                                                @elseif(str_contains($log->action, 'EXPORT')) bg-indigo-100 text-indigo-800
                                                @else bg-gray-100 text-gray-800
                                                @endif
                                            ">
                                                @if(str_contains($log->action, 'CREATE'))
                                                    <i class="fas fa-plus-circle"></i>
                                                @elseif(str_contains($log->action, 'UPDATE'))
                                                    <i class="fas fa-edit"></i>
                                                @elseif(str_contains($log->action, 'DELETE'))
                                                    <i class="fas fa-trash-alt"></i>
                                                @elseif(str_contains($log->action, 'LOGIN'))
                                                    <i class="fas fa-sign-in-alt"></i>
                                                @elseif(str_contains($log->action, 'LOGOUT'))
                                                    <i class="fas fa-sign-out-alt"></i>
                                                @elseif(str_contains($log->action, 'IMPORT'))
                                                    <i class="fas fa-file-import"></i>
                                                @elseif(str_contains($log->action, 'EXPORT'))
                                                    <i class="fas fa-file-export"></i>
                                                @else
                                                    <i class="fas fa-info-circle"></i>
                                                @endif
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <code class="text-xs bg-gray-100 px-2 py-1 rounded">
                                                {{ $log->endpoint ?? 'N/A' }}
                                            </code>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-1 text-xs font-semibold rounded
                                                @if($log->http_method == 'POST') bg-green-100 text-green-800
                                                @elseif($log->http_method == 'GET') bg-blue-100 text-blue-800
                                                @elseif($log->http_method == 'PUT' || $log->http_method == 'PATCH') bg-yellow-100 text-yellow-800
                                                @elseif($log->http_method == 'DELETE') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif
                                            ">
                                                {{ $log->http_method ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->ip_address }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('audit-logs.show', $log) }}"
                                               class="inline-flex items-center gap-1 bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                            No se encontraron registros
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Vista Móvil: Tarjetas -->
                    <div class="md:hidden space-y-4">
                        @forelse($logs as $log)
                            <article class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm" role="article" aria-label="Registro de auditoría {{ $log->id }}">
                                <!-- Cabecera -->
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-xs font-semibold text-gray-500">#{{ $log->id }}</span>
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full items-center gap-1
                                                @if(str_contains($log->action, 'CREATE')) bg-green-100 text-green-800
                                                @elseif(str_contains($log->action, 'UPDATE')) bg-blue-100 text-blue-800
                                                @elseif(str_contains($log->action, 'DELETE')) bg-red-100 text-red-800
                                                @elseif(str_contains($log->action, 'LOGIN')) bg-purple-100 text-purple-800
                                                @elseif(str_contains($log->action, 'LOGOUT')) bg-orange-100 text-orange-800
                                                @elseif(str_contains($log->action, 'IMPORT')) bg-yellow-100 text-yellow-800
                                                @elseif(str_contains($log->action, 'EXPORT')) bg-indigo-100 text-indigo-800
                                                @else bg-gray-100 text-gray-800
                                                @endif
                                            ">
                                                @if(str_contains($log->action, 'CREATE'))
                                                    <i class="fas fa-plus-circle"></i>
                                                @elseif(str_contains($log->action, 'UPDATE'))
                                                    <i class="fas fa-edit"></i>
                                                @elseif(str_contains($log->action, 'DELETE'))
                                                    <i class="fas fa-trash-alt"></i>
                                                @elseif(str_contains($log->action, 'LOGIN'))
                                                    <i class="fas fa-sign-in-alt"></i>
                                                @elseif(str_contains($log->action, 'LOGOUT'))
                                                    <i class="fas fa-sign-out-alt"></i>
                                                @elseif(str_contains($log->action, 'IMPORT'))
                                                    <i class="fas fa-file-import"></i>
                                                @elseif(str_contains($log->action, 'EXPORT'))
                                                    <i class="fas fa-file-export"></i>
                                                @else
                                                    <i class="fas fa-info-circle"></i>
                                                @endif
                                                {{ $log->action }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500" aria-label="Fecha del registro">
                                            <i class="fas fa-clock text-xs"></i>
                                            <time datetime="{{ $log->created_at->toISOString() }}">
                                                {{ $log->created_at->diffForHumans() }}
                                            </time>
                                        </p>
                                    </div>
                                </div>

                                <!-- Información del Usuario -->
                                <div class="mb-3 pb-3 border-b border-gray-100">
                                    <p class="text-xs text-gray-500 mb-1">Usuario</p>
                                    <p class="font-medium text-gray-900">{{ $log->user?->name ?? 'Usuario Eliminado' }}</p>
                                    <p class="text-xs text-gray-500">{{ $log->user?->email ?? 'N/A' }}</p>
                                </div>

                                <!-- Detalles Técnicos -->
                                <div class="grid grid-cols-2 gap-3 mb-3 text-sm">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Método</p>
                                        <span class="px-2 py-1 text-xs font-semibold rounded inline-block
                                            @if($log->http_method == 'POST') bg-green-100 text-green-800
                                            @elseif($log->http_method == 'GET') bg-blue-100 text-blue-800
                                            @elseif($log->http_method == 'PUT' || $log->http_method == 'PATCH') bg-yellow-100 text-yellow-800
                                            @elseif($log->http_method == 'DELETE') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif
                                        ">
                                            {{ $log->http_method ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">IP</p>
                                        <p class="text-xs text-gray-700 font-mono">{{ $log->ip_address }}</p>
                                    </div>
                                </div>

                                <!-- Endpoint -->
                                <div class="mb-3">
                                    <p class="text-xs text-gray-500 mb-1">Endpoint</p>
                                    <code class="text-xs bg-gray-100 px-2 py-1 rounded block truncate">
                                        {{ $log->endpoint ?? 'N/A' }}
                                    </code>
                                </div>

                                <!-- Botón de Acción -->
                                <a href="{{ route('audit-logs.show', $log) }}"
                                   class="inline-flex items-center justify-center gap-2 w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition touch-manipulation active:scale-95"
                                   aria-label="Ver detalles del registro {{ $log->id }}">
                                    <i class="fas fa-eye"></i>
                                    <span>Ver Detalles</span>
                                </a>
                            </article>
                        @empty
                            <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                                <i class="fas fa-inbox text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-500">No se encontraron registros</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Mostrar spinner de carga durante exportación
        function showLoadingSpinner() {
            document.getElementById('loadingSpinner').style.display = 'flex';

            // Ocultar después de 5 segundos (por si hay error)
            setTimeout(() => {
                document.getElementById('loadingSpinner').style.display = 'none';
            }, 5000);
        }

        // Auto-refresh opcional (comentado por defecto)
        // let autoRefreshEnabled = false;
        // let refreshInterval = null;

        // function toggleAutoRefresh() {
        //     autoRefreshEnabled = !autoRefreshEnabled;
        //     if (autoRefreshEnabled) {
        //         refreshInterval = setInterval(() => {
        //             location.reload();
        //         }, 30000); // 30 segundos
        //         console.log('Auto-refresh activado');
        //     } else {
        //         clearInterval(refreshInterval);
        //         console.log('Auto-refresh desactivado');
        //     }
        // }

        // Confirmar antes de aplicar filtros si hay muchos resultados
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.querySelector('form[action*="audit-logs"]');
            if (filterForm) {
                // Agregar indicador de carga en botones
                const submitButtons = filterForm.querySelectorAll('button[type="submit"]');
                submitButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        const originalHTML = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Filtrando...';
                        this.disabled = true;

                        // Restaurar después de 3 segundos si no se envió
                        setTimeout(() => {
                            this.innerHTML = originalHTML;
                            this.disabled = false;
                        }, 3000);
                    });
                });
            }
        });
    </script>
</x-app-layout>

