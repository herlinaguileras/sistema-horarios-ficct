<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Estadísticas de Docentes') }}
            </h2>

        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Resumen General --}}
            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-5">
                <div class="overflow-hidden bg-white rounded-lg shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 w-0 ml-5">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Docentes</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ count($estadisticas) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden bg-white rounded-lg shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 w-0 ml-5">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Asistencias</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ array_sum(array_column($estadisticas, 'total_asistencias')) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden bg-white rounded-lg shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div class="flex-1 w-0 ml-5">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Grupos</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ array_sum(array_column($estadisticas, 'total_grupos')) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden bg-white rounded-lg shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 w-0 ml-5">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Horarios</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ array_sum(array_column($estadisticas, 'total_horarios')) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden bg-white rounded-lg shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                </svg>
                            </div>
                            <div class="flex-1 w-0 ml-5">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">% Promedio</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">
                                        {{ count($estadisticas) > 0 ? round(array_sum(array_column($estadisticas, 'porcentaje_cumplimiento')) / count($estadisticas), 1) : 0 }}%
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla de Estadísticas por Docente --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="mb-4 text-lg font-semibold">Estadísticas por Docente</h3>

                    @if(empty($estadisticas))
                        <p class="text-gray-500">No hay docentes activos registrados.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Docente</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Grupos</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Horarios</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Total Asist.</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">% Cumplimiento</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Índice Constancia</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Frec. Semanal</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Clasificación</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($estadisticas as $stat)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 w-10 h-10">
                                                        <div class="flex items-center justify-center w-10 h-10 text-white bg-blue-500 rounded-full">
                                                            {{ strtoupper(substr($stat['docente']->user->name, 0, 2)) }}
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $stat['docente']->user->name }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            Cód: {{ $stat['docente']->codigo_docente }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center text-gray-900 whitespace-nowrap">
                                                <span class="inline-flex px-2 text-xs font-semibold leading-5 text-blue-800 bg-blue-100 rounded-full">
                                                    {{ $stat['total_grupos'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center text-gray-900 whitespace-nowrap">
                                                <span class="inline-flex px-2 text-xs font-semibold leading-5 text-purple-800 bg-purple-100 rounded-full">
                                                    {{ $stat['total_horarios'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center text-gray-900 whitespace-nowrap">
                                                <span class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">
                                                    {{ $stat['total_asistencias'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center whitespace-nowrap">
                                                <div class="flex flex-col items-center">
                                                    <div class="w-full max-w-xs">
                                                        <div class="flex items-center justify-between mb-1">
                                                            <span class="text-xs font-semibold
                                                                @if($stat['porcentaje_cumplimiento'] >= 90) text-green-600
                                                                @elseif($stat['porcentaje_cumplimiento'] >= 75) text-blue-600
                                                                @elseif($stat['porcentaje_cumplimiento'] >= 60) text-yellow-600
                                                                @else text-red-600
                                                                @endif">
                                                                {{ $stat['porcentaje_cumplimiento'] }}%
                                                            </span>
                                                        </div>
                                                        <div class="w-full h-2 bg-gray-200 rounded-full">
                                                            <div class="h-2 rounded-full
                                                                @if($stat['porcentaje_cumplimiento'] >= 90) bg-green-500
                                                                @elseif($stat['porcentaje_cumplimiento'] >= 75) bg-blue-500
                                                                @elseif($stat['porcentaje_cumplimiento'] >= 60) bg-yellow-500
                                                                @else bg-red-500
                                                                @endif"
                                                                style="width: {{ min($stat['porcentaje_cumplimiento'], 100) }}%">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center whitespace-nowrap">
                                                @if($stat['indice_constancia'] >= 100)
                                                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                        {{ $stat['indice_constancia'] }}%
                                                    </span>
                                                @elseif($stat['indice_constancia'] >= 80)
                                                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                                                        →{{ $stat['indice_constancia'] }}%
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                                                        </svg>
                                                        {{ $stat['indice_constancia'] }}%
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center text-gray-900 whitespace-nowrap">
                                                <div class="flex flex-col items-center">
                                                    <span class="font-semibold text-indigo-600">{{ $stat['frecuencia_semanal'] }}</span>
                                                    <span class="text-xs text-gray-500">reg/sem</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center whitespace-nowrap">
                                                @if($stat['clasificacion'] == 'Excelente')
                                                    <span class="inline-flex px-3 py-1 text-xs font-bold text-green-800 bg-green-200 rounded-full">
                                                        ⭐ {{ $stat['clasificacion'] }}
                                                    </span>
                                                @elseif($stat['clasificacion'] == 'Bueno')
                                                    <span class="inline-flex px-3 py-1 text-xs font-bold text-blue-800 bg-blue-200 rounded-full">
                                                        👍 {{ $stat['clasificacion'] }}
                                                    </span>
                                                @elseif($stat['clasificacion'] == 'Regular')
                                                    <span class="inline-flex px-3 py-1 text-xs font-bold text-yellow-800 bg-yellow-200 rounded-full">
                                                        ⚠️ {{ $stat['clasificacion'] }}
                                                    </span>
                                                @elseif($stat['clasificacion'] == 'Necesita mejorar')
                                                    <span class="inline-flex px-3 py-1 text-xs font-bold text-red-800 bg-red-200 rounded-full">
                                                        ⚡ Mejorar
                                                    </span>
                                                @else
                                                    <span class="inline-flex px-3 py-1 text-xs font-bold text-gray-800 bg-gray-200 rounded-full">
                                                        {{ $stat['clasificacion'] }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium text-center whitespace-nowrap">
                                                <a href="{{ route('estadisticas.show', $stat['docente']) }}"
                                                   class="inline-flex items-center px-3 py-1 text-white bg-blue-600 rounded hover:bg-blue-700">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                    </svg>
                                                    Detalle
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Análisis Estadístico Adicional --}}
            <div class="grid grid-cols-1 gap-6 mt-6 md:grid-cols-3">
                {{-- Distribución por Clasificación --}}
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Distribución por Clasificación</h3>
                        @php
                            $clasificaciones = array_count_values(array_column($estadisticas, 'clasificacion'));
                        @endphp
                        <div class="space-y-3">
                            @foreach(['Excelente' => 'green', 'Bueno' => 'blue', 'Regular' => 'yellow', 'Necesita mejorar' => 'red'] as $clase => $color)
                                @php $count = $clasificaciones[$clase] ?? 0; @endphp
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700">{{ $clase }}</span>
                                        <span class="text-sm font-semibold text-{{ $color }}-600">{{ $count }} ({{ count($estadisticas) > 0 ? round(($count / count($estadisticas)) * 100, 1) : 0 }}%)</span>
                                    </div>
                                    <div class="w-full h-3 bg-gray-200 rounded-full">
                                        <div class="h-3 bg-{{ $color }}-500 rounded-full" style="width: {{ count($estadisticas) > 0 ? ($count / count($estadisticas)) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Top 5 Docentes Más Activos --}}
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Top 5 Más Activos</h3>
                        <div class="space-y-3">
                            @foreach(array_slice($estadisticas, 0, 5) as $index => $stat)
                                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50">
                                    <div class="flex items-center">
                                        <div class="flex items-center justify-center w-8 h-8 mr-3 text-white rounded-full bg-gradient-to-br from-blue-500 to-blue-600">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ Str::limit($stat['docente']->user->name, 20) }}</p>
                                            <p class="text-xs text-gray-500">{{ $stat['total_asistencias'] }} asistencias</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-bold text-green-800 bg-green-100 rounded-full">
                                        {{ $stat['porcentaje_cumplimiento'] }}%
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Alertas y Recomendaciones --}}
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Alertas</h3>
                        <div class="space-y-3">
                            @php
                                $alertas = array_filter($estadisticas, fn($s) => $s['dias_sin_registro'] !== null && $s['dias_sin_registro'] > 7);
                                $alertasConstancia = array_filter($estadisticas, fn($s) => $s['indice_constancia'] < 50);
                            @endphp

                            @if(count($alertas) > 0)
                                <div class="p-3 border-l-4 border-red-500 rounded bg-red-50">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-red-800">Sin registro +7 días</p>
                                            <p class="text-xs text-red-700">{{ count($alertas) }} docente(s)</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(count($alertasConstancia) > 0)
                                <div class="p-3 border-l-4 border-yellow-500 rounded bg-yellow-50">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-yellow-800">Baja constancia</p>
                                            <p class="text-xs text-yellow-700">{{ count($alertasConstancia) }} docente(s)</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @php
                                $excelentes = count(array_filter($estadisticas, fn($s) => $s['clasificacion'] == 'Excelente'));
                            @endphp
                            @if($excelentes > 0)
                                <div class="p-3 border-l-4 border-green-500 rounded bg-green-50">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-green-800">Desempeño Excelente</p>
                                            <p class="text-xs text-green-700">{{ $excelentes }} docente(s)</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
