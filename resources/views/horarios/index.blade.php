<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Gestión de Horarios') }}
            </h2>
            <a href="{{ route('horarios.create') }}" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                + Nuevo Horario
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="px-4 py-3 mb-4 text-green-700 bg-green-100 border border-green-400 rounded">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Formulario de Filtros --}}
                    <div class="p-4 mb-6 bg-gray-50 rounded-lg border border-gray-200">
                        <h5 class="font-semibold text-sm text-gray-700 mb-3">🔍 Filtros de Búsqueda</h5>
                        <form method="GET" action="{{ route('horarios.index') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3">
                            
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Semestre</label>
                                <select name="filtro_semestre_id" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Todos</option>
                                    @foreach($semestres as $semestre)
                                        <option value="{{ $semestre->id }}" {{ ($filtros['filtro_semestre_id'] ?? '') == $semestre->id ? 'selected' : '' }}>
                                            {{ $semestre->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

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

                            <div class="flex items-end space-x-2 md:col-span-3 lg:col-span-6">
                                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    Filtrar
                                </button>
                                <a href="{{ route('horarios.index') }}" class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">
                                    Limpiar
                                </a>
                                <span class="ml-auto text-xs text-gray-500">
                                    Mostrando {{ $horarios->count() }} horario(s)
                                </span>
                            </div>
                        </form>
                    </div>

                    @if($horarios->isEmpty())
                        <p class="text-gray-500">No hay horarios registrados que coincidan con los filtros aplicados.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Semestre</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Grupo</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Materia</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Docente</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Día</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Horario</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Aula</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($horarios as $horario)
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                <span class="inline-flex px-2 text-xs font-semibold leading-5 text-blue-800 bg-blue-100 rounded-full">
                                                    {{ $horario->grupo->semestre->nombre ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                                {{ $horario->grupo->nombre }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $horario->grupo->materia->nombre }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $horario->grupo->docente->user->name }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                @php
                                                    $dias = [
                                                        1 => 'Lunes',
                                                        2 => 'Martes',
                                                        3 => 'Miércoles',
                                                        4 => 'Jueves',
                                                        5 => 'Viernes',
                                                        6 => 'Sábado',
                                                        7 => 'Domingo'
                                                    ];
                                                @endphp
                                                {{ $dias[$horario->dia_semana] ?? 'Día ' . $horario->dia_semana }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ date('H:i', strtotime($horario->hora_inicio)) }} - {{ date('H:i', strtotime($horario->hora_fin)) }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $horario->aula->nombre }}
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                                <div class="flex gap-2">
                                                    <a href="{{ route('horarios.edit', $horario) }}" class="text-blue-600 hover:text-blue-900">
                                                        Editar
                                                    </a>
                                                    <form action="{{ route('horarios.destroy', $horario) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este horario?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
