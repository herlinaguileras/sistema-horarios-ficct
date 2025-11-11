<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Estadísticas Detalladas: {{ $docente->user->name }}
            </h2>
            <p class="text-sm text-gray-600">Código: {{ $docente->codigo_docente }}</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Resumen General del Docente --}}
            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-4">
                <div class="overflow-hidden rounded-lg shadow bg-gradient-to-br from-blue-500 to-blue-600">
                    <div class="p-5 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-100">Total Grupos</p>
                                <p class="text-3xl font-bold">{{ $docente->grupos->count() }}</p>
                            </div>
                            <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg shadow bg-gradient-to-br from-green-500 to-green-600">
                    <div class="p-5 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-green-100">Asistencias Registradas</p>
                                <p class="text-3xl font-bold">{{ $totalAsistenciasRegistradas }}</p>
                            </div>
                            <svg class="w-12 h-12 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg shadow bg-gradient-to-br from-purple-500 to-purple-600">
                    <div class="p-5 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-purple-100">Total Horarios</p>
                                <p class="text-3xl font-bold">{{ $docente->grupos->flatMap->horarios->count() }}</p>
                            </div>
                            <svg class="w-12 h-12 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg shadow bg-gradient-to-br from-orange-500 to-orange-600">
                    <div class="p-5 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-orange-100">Promedio Asistencia</p>
                                <p class="text-3xl font-bold">{{ number_format($promedioAsistenciaDocente, 0) }}%</p>
                                <p class="mt-1 text-xs text-orange-100">{{ $totalClasesDictadas }} de {{ $clasesEsperadasTotal }} clases</p>
                            </div>
                            <svg class="w-12 h-12 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Gráfico de Asistencias por Mes --}}
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">Asistencias por Mes (Últimos 6 meses)</h3>
                    <div class="grid grid-cols-6 gap-4">
                        @foreach($asistenciasPorMes as $mes)
                            <div class="text-center">
                                <div class="relative pt-1">
                                    <div class="flex items-end justify-center h-32 mb-2">
                                        <div class="w-full bg-blue-500 rounded-t" style="height: {{ $mes['cantidad'] > 0 ? min(($mes['cantidad'] / max(array_column($asistenciasPorMes, 'cantidad'))) * 100, 100) : 5 }}%"></div>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-700">{{ $mes['cantidad'] }}</div>
                                </div>
                                <div class="mt-2 text-xs text-gray-600">{{ $mes['mes'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Historial de Asistencias por Materia y Grupo --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">Historial Completo de Asistencias por Materia y Grupo</h3>

                    @forelse($detallesGrupos as $detalle)
                        <div class="mb-6 overflow-hidden border border-gray-200 rounded-lg">
                            {{-- Encabezado de Materia/Grupo --}}
                            <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-lg font-bold text-gray-900">
                                            {{ $detalle['grupo']->materia->sigla }} - {{ $detalle['grupo']->materia->nombre }}
                                        </h4>
                                        <div class="flex items-center mt-1 space-x-4 text-sm text-gray-600">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                Grupo {{ $detalle['grupo']->nombre }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                {{ $detalle['grupo']->semestre->nombre }}
                                            </span>
                                            <span class="flex items-center">
                                                @php
                                                    $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
                                                @endphp
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $dias[$detalle['horario']->dia_semana] ?? 'Día ' . $detalle['horario']->dia_semana }}
                                                {{ date('H:i', strtotime($detalle['horario']->hora_inicio)) }} - {{ date('H:i', strtotime($detalle['horario']->hora_fin)) }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                Aula: {{ $detalle['horario']->aula->nombre }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-3xl font-bold text-blue-600">{{ $detalle['total_asistencias'] }}</div>
                                        <div class="text-sm text-gray-600">Registros totales</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Historial de Asistencias --}}
                            @if(!empty($detalle['historial']))
                                <div class="p-4">
                                    <h5 class="mb-3 text-sm font-semibold text-gray-700">📅 Historial de Asistencias del Semestre</h5>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Fecha</th>
                                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Día</th>
                                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Estudiantes</th>
                                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Método Registro</th>
                                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Hora Registro</th>
                                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($detalle['historial'] as $registro)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 whitespace-nowrap">
                                                            {{ \Carbon\Carbon::parse($registro['fecha'])->format('d/m/Y') }}
                                                        </td>
                                                        <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                                                            {{ \Carbon\Carbon::parse($registro['fecha'])->locale('es')->dayName }}
                                                        </td>
                                                        <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
                                                            <span class="inline-flex px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                                                                {{ $registro['cantidad_estudiantes'] }} estudiante(s)
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
                                                            @if($registro['metodo_registro'] == 'qr')
                                                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM13 3a1 1 0 00-1 1v3a1 1 0 001 1h3a1 1 0 001-1V4a1 1 0 00-1-1h-3zm1 2v1h1V5h-1z" clip-rule="evenodd" />
                                                                        <path d="M11 4a1 1 0 10-2 0v1a1 1 0 002 0V4zM10 7a1 1 0 011 1v1h2a1 1 0 110 2h-3a1 1 0 01-1-1V8a1 1 0 011-1zM16 9a1 1 0 100 2 1 1 0 000-2zM9 13a1 1 0 011-1h1a1 1 0 110 2v2a1 1 0 11-2 0v-3zM7 11a1 1 0 100-2H4a1 1 0 100 2h3zM17 13a1 1 0 01-1 1h-2a1 1 0 110-2h2a1 1 0 011 1zM16 17a1 1 0 100-2h-3a1 1 0 100 2h3z" />
                                                                    </svg>
                                                                    Código QR
                                                                </span>
                                                            @elseif($registro['metodo_registro'] == 'manual')
                                                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                                    </svg>
                                                                    Manual
                                                                </span>
                                                            @else
                                                                <span class="inline-flex px-3 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">
                                                                    {{ ucfirst($registro['metodo_registro'] ?? 'N/A') }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                                                            {{ $registro['hora_registro']->format('H:i:s') }}
                                                        </td>
                                                        <td class="px-4 py-3 text-sm whitespace-nowrap">
                                                            <span class="inline-flex px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                                                ✓ Completado
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="p-6 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">No hay asistencias registradas para este horario en el semestre actual</p>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-6 text-center">
                            <p class="text-gray-500">El docente no tiene grupos asignados actualmente.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
