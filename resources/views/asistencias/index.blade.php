<x-app-layout>
    <x-slot name="header">
        {{-- Título Principal: Detalles del Horario --}}
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Asistencia: {{ $horario->grupo->materia->sigla }} - Gpo {{ $horario->grupo->nombre }}
             (@php $nombresDias = [ 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo']; @endphp {{ $nombresDias[$horario->dia_semana] ?? 'Día inválido' }} {{ date('H:i', strtotime($horario->hora_inicio)) }} - {{ date('H:i', strtotime($horario->hora_fin)) }} en {{ $horario->aula->nombre }})
        </h2>
        {{-- Subtítulo: Docente y Semestre --}}
        <h3 class="text-gray-600 text-md">
            Docente: {{ $horario->grupo->docente->user->name }} | Semestre: {{ $horario->grupo->semestre->nombre }}
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
                        <a href="{{ route('grupos.horarios.index', $horario->grupo) }}" class="text-blue-600 hover:text-blue-900">&larr; Volver al Horario del Grupo</a>

                        {{-- Botón para ir al formulario de registro manual --}}
                        <a href="{{ route('horarios.asistencias.create', $horario) }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-gray-800 border border-transparent rounded-md hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25">
                            Registrar Asistencia Manual
                        </a>
                    </div>

                    {{-- Tabla de Asistencias --}}
                    <h3 class="mb-2 text-lg font-semibold">Historial de Asistencia</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Fecha</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Hora Registro</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Estado</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Método</th>
                                {{-- NUEVO ENCABEZADO --}}
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Justificación</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            {{-- Usamos @forelse para el caso vacío --}}
                            @forelse ($asistencias as $asistencia)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $asistencia->fecha }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ date('H:i:s', strtotime($asistencia->hora_registro)) }}</td> {{-- Formateamos hora --}}
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $asistencia->estado }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $asistencia->metodo_registro ?? 'N/A' }}</td>
                                    {{-- NUEVA CELDA --}}
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $asistencia->justificacion ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                        {{-- Formulario de Eliminación --}}
                                        <form action="{{ route('asistencias.destroy', $asistencia) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    {{-- Ajusta colspan a 6 ahora --}}
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No hay registros de asistencia para este horario todavía.
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
