<div class="mb-6">
    <div class="flex items-center justify-between mb-4">
        <h4 class="font-medium text-md">Asistencia por Docente y Grupo - {{ $semestreActivo ? $semestreActivo->nombre : 'N/A' }}</h4>
        @if ($semestreActivo)
        <div class="flex gap-2">
            <button onclick="submitExportForm('dashboardAsistenciaExportForm', this)"
                    class="inline-flex items-center px-3 py-1 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-green-600 border border-transparent rounded-md hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25">
                <span class="btn-text"><i class="fas fa-file-excel mr-1"></i> Excel</span>
                <span class="btn-loading hidden"><i class="fas fa-spinner fa-spin mr-1"></i> Exportando...</span>
            </button>
            <button onclick="exportPdfWithFilters('{{ route('dashboard.export.asistencia.pdf') }}', 'dashboardAsistenciaPdfFilters')"
               class="inline-flex items-center px-3 py-1 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-red-600 border border-transparent rounded-md hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25">
                <i class="fas fa-file-pdf mr-1"></i> PDF
            </button>
        </div>
        @endif
    </div>
    @error('export_error') <div class="inline-block w-full p-4 mb-4 text-base text-red-700 bg-red-100 rounded-lg" role="alert">{{ $message }}</div> @enderror

    {{-- Formulario de Filtros para Asistencias --}}
    @if ($semestreActivo)
    <div class="p-4 mb-4 bg-gray-50 rounded-lg border border-gray-200">
        <h5 class="font-semibold text-sm text-gray-700 mb-3">🔍 Filtros de Búsqueda</h5>
        <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            <input type="hidden" name="tab" value="asistencias">

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Docente</label>
                <select name="filtro_asist_docente_id" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todos</option>
                    @foreach($docentes as $docente)
                        <option value="{{ $docente->id }}" {{ ($filtros['filtro_asist_docente_id'] ?? '') == $docente->id ? 'selected' : '' }}>
                            {{ $docente->user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Materia</label>
                <select name="filtro_asist_materia_id" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todas</option>
                    @foreach($materias as $materia)
                        <option value="{{ $materia->id }}" {{ ($filtros['filtro_asist_materia_id'] ?? '') == $materia->id ? 'selected' : '' }}>
                            {{ $materia->sigla }} - {{ $materia->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Grupo</label>
                <select name="filtro_asist_grupo_id" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todos</option>
                    @foreach($grupos as $grupo)
                        <option value="{{ $grupo->id }}" {{ ($filtros['filtro_asist_grupo_id'] ?? '') == $grupo->id ? 'selected' : '' }}>
                            {{ $grupo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Estado</label>
                <select name="filtro_asist_estado" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todos</option>
                    <option value="Presente" {{ ($filtros['filtro_asist_estado'] ?? '') == 'Presente' ? 'selected' : '' }}>Presente</option>
                    <option value="Ausente" {{ ($filtros['filtro_asist_estado'] ?? '') == 'Ausente' ? 'selected' : '' }}>Ausente</option>
                    <option value="Justificado" {{ ($filtros['filtro_asist_estado'] ?? '') == 'Justificado' ? 'selected' : '' }}>Justificado</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Método de Registro</label>
                <select name="filtro_asist_metodo" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todos</option>
                    <option value="QR" {{ ($filtros['filtro_asist_metodo'] ?? '') == 'QR' ? 'selected' : '' }}>QR</option>
                    <option value="Manual" {{ ($filtros['filtro_asist_metodo'] ?? '') == 'Manual' ? 'selected' : '' }}>Manual</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Fecha Inicio</label>
                <input type="date" name="filtro_asist_fecha_inicio" value="{{ $filtros['filtro_asist_fecha_inicio'] ?? '' }}"
                    class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Fecha Fin</label>
                <input type="date" name="filtro_asist_fecha_fin" value="{{ $filtros['filtro_asist_fecha_fin'] ?? '' }}"
                    class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Filtrar
                </button>
                <a href="{{ route('dashboard', ['tab' => 'asistencias']) }}" class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    {{-- Formulario oculto para exportar Excel con filtros --}}
    <form id="dashboardAsistenciaExportForm" method="GET" action="{{ route('dashboard.export.asistencia') }}" style="display: none;">
        <input type="hidden" name="filtro_asist_docente_id" value="{{ $filtros['filtro_asist_docente_id'] ?? '' }}">
        <input type="hidden" name="filtro_asist_materia_id" value="{{ $filtros['filtro_asist_materia_id'] ?? '' }}">
        <input type="hidden" name="filtro_asist_grupo_id" value="{{ $filtros['filtro_asist_grupo_id'] ?? '' }}">
        <input type="hidden" name="filtro_asist_estado" value="{{ $filtros['filtro_asist_estado'] ?? '' }}">
        <input type="hidden" name="filtro_asist_metodo" value="{{ $filtros['filtro_asist_metodo'] ?? '' }}">
        <input type="hidden" name="filtro_asist_fecha_inicio" value="{{ $filtros['filtro_asist_fecha_inicio'] ?? '' }}">
        <input type="hidden" name="filtro_asist_fecha_fin" value="{{ $filtros['filtro_asist_fecha_fin'] ?? '' }}">
    </form>

    {{-- Contenedor oculto con filtros para PDF --}}
    <div id="dashboardAsistenciaPdfFilters" style="display: none;"
         data-filtro_asist_docente_id="{{ $filtros['filtro_asist_docente_id'] ?? '' }}"
         data-filtro_asist_materia_id="{{ $filtros['filtro_asist_materia_id'] ?? '' }}"
         data-filtro_asist_grupo_id="{{ $filtros['filtro_asist_grupo_id'] ?? '' }}"
         data-filtro_asist_estado="{{ $filtros['filtro_asist_estado'] ?? '' }}"
         data-filtro_asist_metodo="{{ $filtros['filtro_asist_metodo'] ?? '' }}"
         data-filtro_asist_fecha_inicio="{{ $filtros['filtro_asist_fecha_inicio'] ?? '' }}"
         data-filtro_asist_fecha_fin="{{ $filtros['filtro_asist_fecha_fin'] ?? '' }}">
    </div>
    @endif
</div>

@if ($semestreActivo && isset($asistenciasAgrupadas) && !$asistenciasAgrupadas->isEmpty())
    @foreach ($asistenciasAgrupadas as $docenteId => $gruposDelDocente)
        @php $primerRegistroDocente = $gruposDelDocente->first()->first(); @endphp
        <div class="p-4 mb-6 border rounded-lg shadow-sm bg-gray-50">
            <h5 class="mb-3 text-lg font-semibold text-gray-700">Docente: {{ $primerRegistroDocente->docente->user->name ?? 'Docente Desconocido' }}</h5>
            @foreach ($gruposDelDocente as $grupoId => $asistenciasDelGrupo)
                @php $primerRegistroGrupo = $asistenciasDelGrupo->first(); @endphp
                <div class="pl-4 mb-4 border-l-4 border-indigo-300">
                    <h6 class="mb-2 font-medium text-indigo-800 text-md">
                        Materia: {{ $primerRegistroGrupo->horario->grupo->materia->sigla ?? 'N/A' }} - Grupo {{ $primerRegistroGrupo->horario->grupo->nombre ?? 'N/A' }} ({{ $primerRegistroGrupo->horario->grupo->materia->nombre ?? 'N/A' }})
                    </h6>
                    <table class="min-w-full mb-2 text-sm divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-1 font-medium tracking-wider text-left text-gray-500 uppercase">Fecha</th>
                                <th class="px-3 py-1 font-medium tracking-wider text-left text-gray-500 uppercase">Hora Reg.</th>
                                <th class="px-3 py-1 font-medium tracking-wider text-left text-gray-500 uppercase">Estado</th>
                                <th class="px-3 py-1 font-medium tracking-wider text-left text-gray-500 uppercase">Método</th>
                                <th class="px-3 py-1 font-medium tracking-wider text-left text-gray-500 uppercase">Justificación</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($asistenciasDelGrupo as $asistencia)
                                <tr>
                                    <td class="px-3 py-1 whitespace-nowrap">{{ $asistencia->fecha }}</td>
                                    <td class="px-3 py-1 whitespace-nowrap">{{ date('H:i:s', strtotime($asistencia->hora_registro)) }}</td>
                                    <td class="px-3 py-1 whitespace-nowrap">{{ $asistencia->estado }}</td>
                                    <td class="px-3 py-1 whitespace-nowrap">{{ $asistencia->metodo_registro ?? '-' }}</td>
                                    <td class="px-3 py-1 text-xs text-gray-600 whitespace-nowrap">{{ $asistencia->justificacion ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    @endforeach
@elseif ($semestreActivo)
   <p class="text-gray-500">No hay registros de asistencia para el semestre activo ({{ $semestreActivo->nombre }}).</p>
@else
   <p class="text-red-500">No se encontró un semestre activo.</p>
@endif
