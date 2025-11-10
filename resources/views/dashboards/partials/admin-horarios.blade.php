@if ($semestreActivo)
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-medium text-md">Horario Semanal - {{ $semestreActivo->nombre }}</h4>
            <div>
                <button onclick="document.getElementById('exportFormHorario').submit()" class="inline-flex items-center px-3 py-1 ml-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-green-600 border border-transparent rounded-md hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25">Excel</button>
                <a href="{{ route('dashboard.export.horario.pdf') }}" class="inline-flex items-center px-3 py-1 ml-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-red-600 border border-transparent rounded-md hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25">PDF</a>
            </div>
        </div>
        @error('export_error') <div class="inline-block w-full p-4 mb-4 text-base text-red-700 bg-red-100 rounded-lg" role="alert">{{ $message }}</div> @enderror

        {{-- Formulario de Filtros para Horarios --}}
        <div class="p-4 mb-4 bg-gray-50 rounded-lg border border-gray-200">
            <h5 class="font-semibold text-sm text-gray-700 mb-3">🔍 Filtros de Búsqueda</h5>
            <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-3">
                <input type="hidden" name="tab" value="horarios">
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Docente</label>
                    <select name="filtro_docente_id" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todos</option>
                        @foreach($docentes as $docente)
                            <option value="{{ $docente->id }}" {{ ($filtros['filtro_docente_id'] ?? '') == $docente->id ? 'selected' : '' }}>
                                {{ $docente->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Materia</label>
                    <select name="filtro_materia_id" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todas</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}" {{ ($filtros['filtro_materia_id'] ?? '') == $materia->id ? 'selected' : '' }}>
                                {{ $materia->sigla }} - {{ $materia->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Grupo</label>
                    <select name="filtro_grupo_id" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todos</option>
                        @foreach($grupos as $grupo)
                            <option value="{{ $grupo->id }}" {{ ($filtros['filtro_grupo_id'] ?? '') == $grupo->id ? 'selected' : '' }}>
                                {{ $grupo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Aula</label>
                    <select name="filtro_aula_id" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todas</option>
                        @foreach($aulas as $aula)
                            <option value="{{ $aula->id }}" {{ ($filtros['filtro_aula_id'] ?? '') == $aula->id ? 'selected' : '' }}>
                                {{ $aula->nombre }} (P.{{ $aula->piso }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Día de la Semana</label>
                    <select name="filtro_dia_semana" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todos</option>
                        @foreach($diasSemana as $numDia => $nombreDia)
                            <option value="{{ $numDia }}" {{ ($filtros['filtro_dia_semana'] ?? '') == $numDia ? 'selected' : '' }}>
                                {{ $nombreDia }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Filtrar
                    </button>
                    <a href="{{ route('dashboard', ['tab' => 'horarios']) }}" class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        {{-- Formulario oculto para exportar con filtros --}}
        <form id="exportFormHorario" method="GET" action="{{ route('dashboard.export.horario') }}" style="display: none;">
            <input type="hidden" name="filtro_docente_id" value="{{ $filtros['filtro_docente_id'] ?? '' }}">
            <input type="hidden" name="filtro_materia_id" value="{{ $filtros['filtro_materia_id'] ?? '' }}">
            <input type="hidden" name="filtro_grupo_id" value="{{ $filtros['filtro_grupo_id'] ?? '' }}">
            <input type="hidden" name="filtro_aula_id" value="{{ $filtros['filtro_aula_id'] ?? '' }}">
            <input type="hidden" name="filtro_dia_semana" value="{{ $filtros['filtro_dia_semana'] ?? '' }}">
        </form>
    </div>

    @forelse ($diasSemana as $numDia => $nombreDia)
        @if ($horariosPorDia->has($numDia))
            <div class="p-4 mb-6 border rounded-lg shadow-sm">
                <h5 class="mb-3 text-lg font-semibold text-indigo-700">{{ $nombreDia }}</h5>
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 font-medium tracking-wider text-left text-gray-500 uppercase">Hora</th>
                            <th class="px-4 py-2 font-medium tracking-wider text-left text-gray-500 uppercase">Materia</th>
                            <th class="px-4 py-2 font-medium tracking-wider text-left text-gray-500 uppercase">Grupo</th>
                            <th class="px-4 py-2 font-medium tracking-wider text-left text-gray-500 uppercase">Docente</th>
                            <th class="px-4 py-2 font-medium tracking-wider text-left text-gray-500 uppercase">Aula</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($horariosPorDia[$numDia] as $horario)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap">{{ date('H:i', strtotime($horario->hora_inicio)) }} - {{ date('H:i', strtotime($horario->hora_fin)) }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $horario->grupo->materia->sigla }} - {{ $horario->grupo->materia->nombre }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $horario->grupo->nombre }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $horario->grupo->docente->user->name }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $horario->aula->nombre }} (P.{{ $horario->aula->piso }})</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @empty
        <p class="text-gray-500">No hay días definidos o datos para mostrar.</p>
    @endforelse

    @if ($horariosPorDia->isEmpty() && !empty($diasSemana))
       <p class="text-gray-500">No hay horarios programados para el semestre activo ({{ $semestreActivo->nombre }}).</p>
    @endif
@else
    <p class="text-red-500">No se encontró un semestre activo.</p>
@endif
