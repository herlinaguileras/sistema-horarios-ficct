<x-app-layout>
    <x-slot name="header">
        {{-- Título Principal: Usa $grupo --}}
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Gestionar Horario: {{ $grupo->materia->sigla }} - Grupo {{ $grupo->nombre }}
        </h2>
        {{-- Subtítulo: Usa $grupo --}}
        <h3 class="text-gray-600 text-md">
            Docente: {{ $grupo->docente->user->name }} | Semestre: {{ $grupo->semestre->nombre }}
        </h3>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Mensaje de éxito --}}
                    @if (session('status'))
                        <div class="inline-block w-full p-4 mb-4 text-base text-green-700 bg-green-100 rounded-lg" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Botones de Acción --}}
                    <div class="flex items-center justify-between mb-4">
                        {{-- Volver a la lista de horarios del grupo --}}
                        <a href="{{ route('grupos.index') }}" class="text-blue-600 hover:text-blue-900">&larr; Volver a la lista de Grupos</a>

                        {{-- Botón para ir al formulario de creación --}}
                        <a href="{{ route('grupos.horarios.create', $grupo) }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-gray-800 border border-transparent rounded-md hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25">
                            Añadir Horario
                        </a>
                    </div>

                    {{-- Tabla de Horarios --}}
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Día</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Hora Inicio</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Hora Fin</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Aula</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            {{-- Usamos @forelse para manejar el caso de que no haya horarios --}}
                            @forelse ($horarios as $horario)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $nombresDias = [ 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
                                        @endphp
                                        {{ $nombresDias[$horario->dia_semana] ?? 'Día inválido' }}
                                    </td>
                                    {{-- Formateamos la hora para quitar los segundos --}}
                                    <td class="px-6 py-4 whitespace-nowrap">{{ date('H:i', strtotime($horario->hora_inicio)) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ date('H:i', strtotime($horario->hora_fin)) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $horario->aula->nombre }} (Piso {{ $horario->aula->piso }})</td>

                                    {{-- Celda de Acciones --}}
                                    <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">

                                        {{-- Botón para ver la asistencia de este horario --}}
                                        <a href="{{ route('horarios.asistencias.index', $horario) }}" class="mr-4 text-green-600 hover:text-green-900">
                                            Asistencia
                                        </a>

                                        {{-- === FORMULARIO DE ELIMINACIÓN === --}}
                                        <form action="{{ route('horarios.destroy', $horario) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                Eliminar
                                            </button>
                                        </form>
                                        {{-- === FIN FORMULARIO === --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No hay horarios registrados para este grupo todavía.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
