<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg sm:text-xl font-semibold leading-tight text-gray-800">
            {{ __('Panel del Docente') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">

                   {{-- Display Success Message --}}
                    @if (session('status'))
                        <div class="inline-block w-full p-3 sm:p-4 mb-3 sm:mb-4 text-sm sm:text-base text-green-700 bg-green-100 rounded-lg" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Display Attendance Errors --}}
                    @if ($errors->any())
                        @foreach ($errors->getMessages() as $key => $messages)
                            {{-- Check if the error key starts with 'asistencia_error_' --}}
                            @if (Illuminate\Support\Str::startsWith($key, 'asistencia_error_'))
                                <div class="inline-block w-full p-3 sm:p-4 mb-3 sm:mb-4 text-sm sm:text-base text-red-700 bg-red-100 rounded-lg" role="alert">
                                    {{ $messages[0] }} {{-- Display the first message for that key --}}
                                </div>
                                @break {{-- Show only the first attendance error --}}
                            @endif
                        @endforeach
                    @endif

                    {{-- Check if a docente profile exists for the user --}}
                    @if ($docente)
                        <h3 class="mb-3 sm:mb-4 text-base sm:text-lg font-semibold">Bienvenido/a, {{ $docente->user->name ?? 'Docente' }}</h3>

                        @if ($semestreActivo)
                            <h4 class="mb-2 font-medium text-sm sm:text-md">Tu Horario Semanal - {{ $semestreActivo->nombre }}</h4>

                            @if($horariosDocente->isEmpty())
                                <p class="text-gray-500">No tienes clases asignadas para este semestre.</p>
                            @else
                                {{-- Loop through the defined order of days --}}
                                @foreach ($diasSemana as $numDia => $nombreDia)
                                    {{-- Check if there are any schedules for this day --}}
                                    @if ($horariosDocente->has($numDia))
                                        <div class="p-3 sm:p-4 mb-4 sm:mb-6 border rounded-lg shadow-sm">
                                            <h5 class="mb-3 text-base sm:text-lg font-semibold text-indigo-700">{{ $nombreDia }}</h5>
                                            <div class="overflow-x-auto -mx-3 sm:mx-0">
                                                <table class="min-w-full text-xs sm:text-sm divide-y divide-gray-200">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th class="px-2 sm:px-4 py-2 font-medium tracking-wider text-left text-gray-500 uppercase text-xs">Hora</th>
                                                            <th class="px-2 sm:px-4 py-2 font-medium tracking-wider text-left text-gray-500 uppercase text-xs">Materia</th>
                                                            <th class="px-2 sm:px-4 py-2 font-medium tracking-wider text-left text-gray-500 uppercase text-xs hidden sm:table-cell">Grupo</th>
                                                            <th class="px-2 sm:px-4 py-2 font-medium tracking-wider text-left text-gray-500 uppercase text-xs hidden md:table-cell">Aula</th>
                                                            <th class="px-2 sm:px-4 py-2 font-medium tracking-wider text-left text-gray-500 uppercase text-xs">Acciones</th>
                                                        </tr>
                                                    </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    {{-- Loop through the schedules for the current day --}}
                                                    @foreach ($horariosDocente[$numDia] as $horario)
                                                        <tr>
                                                            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm">
                                                                <span class="hidden sm:inline">{{ date('H:i', strtotime($horario->hora_inicio)) }} - {{ date('H:i', strtotime($horario->hora_fin)) }}</span>
                                                                <span class="sm:hidden">{{ date('H:i', strtotime($horario->hora_inicio)) }}</span>
                                                            </td>
                                                            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm">
                                                                <div class="font-medium">{{ $horario->grupo->materia->sigla }}</div>
                                                                <div class="hidden sm:block text-gray-600">{{ $horario->grupo->materia->nombre }}</div>
                                                                <div class="sm:hidden text-gray-600 text-xs">{{ $horario->grupo->nombre }}</div>
                                                            </td>
                                                            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm hidden sm:table-cell">{{ $horario->grupo->nombre }}</td>
                                                            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm hidden md:table-cell">{{ $horario->aula->nombre }} (P.{{ $horario->aula->piso }})</td>
                                                            {{-- Action Cell --}}
                                                            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm">
                                                                <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                                                                    {{-- NEW QR Link --}}
                                                                    <a href="{{ route('horarios.qr', $horario) }}" target="_blank" class="text-purple-600 hover:text-purple-900 whitespace-nowrap">
                                                                        <span class="sm:hidden">QR</span>
                                                                        <span class="hidden sm:inline">Mostrar QR</span>
                                                                    </a>
                                                                    {{-- Link to view attendance --}}
                                                                    <a href="{{ route('horarios.asistencias.index', $horario) }}" class="text-green-600 hover:text-green-900 whitespace-nowrap">
                                                                        <span class="sm:hidden">Asistencias</span>
                                                                        <span class="hidden sm:inline">Ver Asistencias</span>
                                                                    </a>

                                                                    {{-- Button to mark attendance (ONLY IF IT'S TODAY) --}}
                                                                    @if ($horario->dia_semana == now()->dayOfWeekIso)
                                                                        <form action="{{ route('asistencias.marcar', $horario) }}" method="POST">
                                                                            @csrf
                                                                            <button type="submit" class="text-blue-600 hover:text-blue-900 whitespace-nowrap">
                                                                                <span class="sm:hidden">Marcar</span>
                                                                                <span class="hidden sm:inline">Marcar Asistencia</span>
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        @else
                            <p class="text-red-500">No hay un semestre activo configurado en el sistema.</p>
                        @endif
                    @else
                        <p class="text-red-500">Tu cuenta de usuario no está vinculada a un perfil de docente.</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
