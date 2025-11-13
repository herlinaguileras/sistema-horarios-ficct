<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                <i class="text-blue-500 fas fa-chart-bar"></i> {{ __('Estadísticas de Bitácora') }}
            </h2>
            <div class="flex gap-2">
                <button onclick="location.reload()" class="inline-flex items-center gap-2 px-4 py-2 text-white transition bg-gray-500 rounded hover:bg-gray-600">
                    <i class="fas fa-sync-alt"></i> Actualizar
                </button>
                <a href="{{ route('audit-logs.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-white transition bg-blue-500 rounded hover:bg-blue-600">
                    <i class="fas fa-list"></i> Ver Bitácora
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

            <!-- Tarjetas de Métricas Clave -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                <!-- Total de Logs -->
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total de Logs</p>
                                <p class="mt-1 text-3xl font-bold text-gray-800">{{ number_format($stats['total_logs']) }}</p>
                                <p class="mt-1 text-xs text-gray-400">Todos los registros</p>
                            </div>
                            <div class="p-4 bg-blue-100 rounded-full">
                                <i class="text-3xl text-blue-500 fas fa-database"></i>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-2 bg-blue-50">
                        <p class="text-xs text-blue-600">
                            <i class="fas fa-calendar-week"></i> Esta semana: <strong>{{ number_format($stats['logs_this_week']) }}</strong>
                        </p>
                    </div>
                </div>

                <!-- Logs Hoy -->
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Logs Hoy</p>
                                <p class="mt-1 text-3xl font-bold text-green-600">{{ number_format($stats['logs_today']) }}</p>
                                <p class="mt-1 text-xs text-gray-400">Actividad del día</p>
                            </div>
                            <div class="p-4 bg-green-100 rounded-full">
                                <i class="text-3xl text-green-500 fas fa-calendar-day"></i>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-2 bg-green-50">
                        <p class="text-xs text-green-600">
                            <i class="fas fa-calendar-alt"></i> Este mes: <strong>{{ number_format($stats['logs_this_month']) }}</strong>
                        </p>
                    </div>
                </div>

                <!-- Usuarios Activos -->
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Usuarios Activos</p>
                                <p class="mt-1 text-3xl font-bold text-purple-600">{{ number_format($stats['active_users']) }}</p>
                                <p class="mt-1 text-xs text-gray-400">Con actividad registrada</p>
                            </div>
                            <div class="p-4 bg-purple-100 rounded-full">
                                <i class="text-3xl text-purple-500 fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-2 bg-purple-50">
                        <p class="text-xs text-purple-600">
                            <i class="fas fa-user-check"></i> Usuarios únicos
                        </p>
                    </div>
                </div>

                <!-- Acciones Críticas (Eliminaciones) -->
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Eliminaciones</p>
                                <p class="mt-1 text-3xl font-bold text-red-600">{{ number_format($stats['deletions']) }}</p>
                                <p class="mt-1 text-xs text-gray-400">Acciones DELETE</p>
                            </div>
                            <div class="p-4 bg-red-100 rounded-full">
                                <i class="text-3xl text-red-500 fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-2 bg-red-50">
                        <p class="text-xs text-red-600">
                            <i class="fas fa-trash-alt"></i> Operaciones críticas
                        </p>
                    </div>
                </div>
            </div>

            <!-- Gráfica de Actividad -->
            <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">
                        <i class="text-indigo-500 fas fa-chart-line"></i> Actividad de los Últimos 30 Días
                    </h3>
                    <canvas id="activityChart" height="80"></canvas>
                </div>
            </div>

            <!-- Grid de Tablas -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Top Acciones -->
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">
                            <i class="text-orange-500 fas fa-fire"></i> Top 10 Acciones
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">#</th>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">Acción</th>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">Total</th>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">%</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($stats['top_actions'] as $index => $action)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm font-bold text-gray-900">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                    @if(str_contains($action->action, 'CREATE')) bg-green-100 text-green-800
                                                    @elseif(str_contains($action->action, 'UPDATE')) bg-blue-100 text-blue-800
                                                    @elseif(str_contains($action->action, 'DELETE')) bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif
                                                ">
                                                    {{ $action->action }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ number_format($action->total) }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-500">
                                                {{ number_format(($action->total / $stats['total_logs']) * 100, 1) }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Top Usuarios -->
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">
                            <i class="text-yellow-500 fas fa-user-friends"></i> Top 10 Usuarios Activos
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">#</th>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">Usuario</th>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($stats['top_users'] as $index => $userStat)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm font-bold text-gray-900">
                                                @if($index == 0) 🥇
                                                @elseif($index == 1) 🥈
                                                @elseif($index == 2) 🥉
                                                @else {{ $index + 1 }}
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <div class="flex items-center gap-2">
                                                    <div class="flex items-center justify-center w-8 h-8 text-xs font-bold text-white bg-blue-500 rounded-full">
                                                        {{ substr($userStat->user?->name ?? 'S', 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-gray-900">{{ $userStat->user?->name ?? 'Usuario Eliminado' }}</div>
                                                        <div class="text-xs text-gray-500">{{ $userStat->user?->email ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <span class="px-3 py-1 font-bold text-blue-800 bg-blue-100 rounded-full">
                                                    {{ number_format($userStat->total) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Top Endpoints -->
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">
                            <i class="text-green-500 fas fa-link"></i> Top 10 Endpoints
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">#</th>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">Endpoint</th>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">Hits</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($stats['top_endpoints'] as $index => $endpoint)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm font-bold text-gray-900">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3 text-xs">
                                                <code class="px-2 py-1 text-gray-700 bg-gray-100 rounded">{{ Str::limit($endpoint->endpoint, 40) }}</code>
                                            </td>
                                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ number_format($endpoint->total) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Top IPs -->
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">
                            <i class="text-red-500 fas fa-network-wired"></i> Top 10 Direcciones IP
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">#</th>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">Dirección IP</th>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">Actividad</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($stats['top_ips'] as $index => $ip)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm font-bold text-gray-900">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                <code class="px-2 py-1 font-mono text-red-700 rounded bg-red-50">{{ $ip->ip_address }}</code>
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-grow h-2 bg-gray-200 rounded-full">
                                                        <div class="h-2 bg-red-500 rounded-full" style="width: {{ min(100, ($ip->total / max($stats['top_ips'][0]->total, 1)) * 100) }}%"></div>
                                                    </div>
                                                    <span class="font-semibold text-gray-900">{{ number_format($ip->total) }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('activityChart').getContext('2d');
        const activityData = @json($stats['activity_by_day']);

        // Ordenar por fecha ascendente
        const sortedData = activityData.sort((a, b) => new Date(a.date) - new Date(b.date));

        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: sortedData.map(item => {
                    const date = new Date(item.date);
                    return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
                }),
                datasets: [{
                    label: 'Número de Logs',
                    data: sortedData.map(item => item.count),
                    backgroundColor: 'rgba(59, 130, 246, 0.6)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    borderRadius: 4,
                    hoverBackgroundColor: 'rgba(59, 130, 246, 0.8)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Logs: ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 11
                            },
                            maxRotation: 45,
                            minRotation: 45
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
