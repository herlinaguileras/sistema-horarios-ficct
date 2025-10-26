<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Panel de Control Administrativo') }}
        </h2>
    </x-slot>

    {{-- Alpine.js component: uses $activeTab passed from controller --}}
    <div class="py-6 sm:py-12" x-data="{ activeTab: '{{ $activeTab ?? 'horarios' }}' }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">
                    <h3 class="mb-4 text-base sm:text-lg font-semibold">Reportes</h3>

                    {{-- Pestañas de Navegación --}}
                    <div class="mb-6 border-b border-gray-200 overflow-x-auto">
                        <nav class="flex -mb-px space-x-4 sm:space-x-8 min-w-max" aria-label="Tabs">
                            {{-- Tab Horarios --}}
                            <button
                                @click="activeTab = 'horarios'"
                                :class="{
                                    'border-indigo-500 text-indigo-600': activeTab === 'horarios',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'horarios'
                                }"
                                class="px-1 py-3 sm:py-4 text-xs sm:text-sm font-medium border-b-2 whitespace-nowrap"
                            >
                                Horario Semanal
                            </button>

                            {{-- Tab Asistencias --}}
                            <button
                                @click="activeTab = 'asistencias'"
                                :class="{
                                    'border-indigo-500 text-indigo-600': activeTab === 'asistencias',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'asistencias'
                                }"
                                class="px-1 py-3 sm:py-4 text-xs sm:text-sm font-medium border-b-2 whitespace-nowrap"
                            >
                                <span class="hidden sm:inline">Asistencia Docente/Grupo</span>
                                <span class="sm:hidden">Asistencias</span>
                            </button>

                            {{-- Tab Aulas Disponibles --}}
                            <button
                                @click="activeTab = 'aulas'"
                                :class="{
                                    'border-indigo-500 text-indigo-600': activeTab === 'aulas',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'aulas'
                                }"
                                class="px-1 py-3 sm:py-4 text-xs sm:text-sm font-medium border-b-2 whitespace-nowrap"
                            >
                                <span class="hidden sm:inline">Aulas Disponibles</span>
                                <span class="sm:hidden">Aulas</span>
                            </button>
                        </nav>
                    </div>

                    {{-- Contenido de las Pestañas (se muestra condicionalmente) --}}

                    {{-- Contenido Pestaña Horarios --}}
                    <div x-show="activeTab === 'horarios'">
                        @if ($semestreActivo)
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-3">
                                <h4 class="font-medium text-sm sm:text-md">Horario Semanal - {{ $semestreActivo->nombre }}</h4>
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('dashboard.export.horario') }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-green-600 border border-transparent rounded-md hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25">
                                        <span>Excel</span>
                                    </a>
                                    <a href="{{ route('dashboard.export.horario.pdf') }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-red-600 border border-transparent rounded-md hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25">
                                        <span>PDF</span>
                                    </a>
                                </div>
                            </div>
                            @error('export_error')
                                <div class="w-full p-3 sm:p-4 mb-4 text-sm sm:text-base text-red-700 bg-red-100 rounded-lg" role="alert">{{ $message }}</div>
                            @enderror

                            @forelse ($diasSemana as $numDia => $nombreDia)
                                @if ($horariosPorDia->has($numDia))
                                    <div class="p-3 sm:p-4 mb-4 sm:mb-6 border rounded-lg shadow-sm">
                                        <h5 class="mb-3 text-base sm:text-lg font-semibold text-indigo-700">{{ $nombreDia }}</h5>
                                        <div class="overflow-x-auto -mx-3 sm:mx-0">
                                            <table class="min-w-full text-xs sm:text-sm divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-2 sm:px-4 py-2 font-medium tracking-wider text-left text-gray-500 uppercase text-xs">Hora</th>
                                                        <th class="px-2 sm:px-4 py-2 font-medium tracking-wider text-left text-gray-500 uppercase text-xs">Materia</th>
                                                        <th class="px-2 sm:px-4 py-2 font-medium tracking-wider text-left text-gray-500 uppercase text-xs">Grupo</th>
                                                        <th class="px-2 sm:px-4 py-2 font-medium tracking-wider text-left text-gray-500 uppercase text-xs">Docente</th>
                                                        <th class="px-2 sm:px-4 py-2 font-medium tracking-wider text-left text-gray-500 uppercase text-xs">Aula</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @foreach ($horariosPorDia[$numDia] as $horario)
                                                        <tr>
                                                            <td class="px-2 sm:px-4 py-2 whitespace-nowrap text-xs sm:text-sm">
                                                                {{ date('H:i', strtotime($horario->hora_inicio)) }}<span class="hidden sm:inline"> - {{ date('H:i', strtotime($horario->hora_fin)) }}</span>
                                                            </td>
                                                            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm">
                                                                <span class="font-medium">{{ $horario->grupo->materia->sigla }}</span>
                                                                <span class="hidden lg:inline"> - {{ $horario->grupo->materia->nombre }}</span>
                                                            </td>
                                                            <td class="px-2 sm:px-4 py-2 whitespace-nowrap text-xs sm:text-sm">{{ $horario->grupo->nombre }}</td>
                                                            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm">{{ $horario->grupo->docente->user->name }}</td>
                                                            <td class="px-2 sm:px-4 py-2 whitespace-nowrap text-xs sm:text-sm">{{ $horario->aula->nombre }} <span class="text-gray-500">(P.{{ $horario->aula->piso }})</span></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <p class="text-gray-500 text-sm">No hay días definidos o datos para mostrar.</p>
                            @endforelse

                            @if ($horariosPorDia->isEmpty() && !empty($diasSemana))
                               <p class="text-gray-500 text-sm">No hay horarios programados para el semestre activo ({{ $semestreActivo->nombre }}).</p>
                            @endif
                        @else
                            <p class="text-red-500 text-sm">No se encontró un semestre activo.</p>
                        @endif
                    </div>

                    {{-- Contenido Pestaña Asistencias --}}
                    <div x-show="activeTab === 'asistencias'">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-3">
                            <h4 class="font-medium text-sm sm:text-md">Asistencia por Docente y Grupo - {{ $semestreActivo ? $semestreActivo->nombre : 'N/A' }}</h4>
                            @if ($semestreActivo)
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('dashboard.export.asistencia') }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-green-600 border border-transparent rounded-md hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25">Excel</a>
                                <a href="{{ route('dashboard.export.asistencia.pdf') }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-red-600 border border-transparent rounded-md hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25">PDF</a>
                            </div>
                            @endif
                        </div>
                        @error('export_error')
                            <div class="w-full p-3 sm:p-4 mb-4 text-sm sm:text-base text-red-700 bg-red-100 rounded-lg" role="alert">{{ $message }}</div>
                        @enderror

                         @if ($semestreActivo && isset($asistenciasAgrupadas) && !$asistenciasAgrupadas->isEmpty())
                             @foreach ($asistenciasAgrupadas as $docenteId => $gruposDelDocente)
                                 @php $primerRegistroDocente = $gruposDelDocente->first()->first(); @endphp
                                 <div class="p-3 sm:p-4 mb-4 sm:mb-6 border rounded-lg shadow-sm bg-gray-50">
                                     <h5 class="mb-3 text-base sm:text-lg font-semibold text-gray-700">Docente: {{ $primerRegistroDocente->docente->user->name ?? 'Docente Desconocido' }}</h5>
                                     @foreach ($gruposDelDocente as $grupoId => $asistenciasDelGrupo)
                                         @php $primerRegistroGrupo = $asistenciasDelGrupo->first(); @endphp
                                         <div class="pl-3 sm:pl-4 mb-4 border-l-4 border-indigo-300">
                                             <h6 class="mb-2 font-medium text-indigo-800 text-sm sm:text-md">
                                                 Materia: {{ $primerRegistroGrupo->horario->grupo->materia->sigla ?? 'N/A' }} - Grupo {{ $primerRegistroGrupo->horario->grupo->nombre ?? 'N/A' }}
                                                 <span class="hidden sm:inline">({{ $primerRegistroGrupo->horario->grupo->materia->nombre ?? 'N/A' }})</span>
                                             </h6>
                                             <div class="overflow-x-auto -mx-3 sm:mx-0">
                                                 <table class="min-w-full mb-2 text-xs sm:text-sm divide-y divide-gray-200">
                                                     <thead class="bg-gray-100">
                                                         <tr>
                                                             <th class="px-2 sm:px-3 py-1 font-medium tracking-wider text-left text-gray-500 uppercase text-xs">Fecha</th>
                                                             <th class="px-2 sm:px-3 py-1 font-medium tracking-wider text-left text-gray-500 uppercase text-xs">Hora</th>
                                                             <th class="px-2 sm:px-3 py-1 font-medium tracking-wider text-left text-gray-500 uppercase text-xs">Estado</th>
                                                             <th class="px-2 sm:px-3 py-1 font-medium tracking-wider text-left text-gray-500 uppercase text-xs hidden sm:table-cell">Método</th>
                                                             <th class="px-2 sm:px-3 py-1 font-medium tracking-wider text-left text-gray-500 uppercase text-xs hidden md:table-cell">Justificación</th>
                                                         </tr>
                                                     </thead>
                                                     <tbody class="bg-white divide-y divide-gray-200">
                                                         @foreach ($asistenciasDelGrupo as $asistencia)
                                                             <tr>
                                                                 <td class="px-2 sm:px-3 py-1 whitespace-nowrap text-xs sm:text-sm">{{ $asistencia->fecha }}</td>
                                                                 <td class="px-2 sm:px-3 py-1 whitespace-nowrap text-xs sm:text-sm">{{ date('H:i', strtotime($asistencia->hora_registro)) }}</td>
                                                                 <td class="px-2 sm:px-3 py-1 whitespace-nowrap text-xs sm:text-sm">{{ $asistencia->estado }}</td>
                                                                 <td class="px-2 sm:px-3 py-1 whitespace-nowrap text-xs sm:text-sm hidden sm:table-cell">{{ $asistencia->metodo_registro ?? '-' }}</td>
                                                                 <td class="px-2 sm:px-3 py-1 text-xs text-gray-600 hidden md:table-cell">{{ $asistencia->justificacion ?? '-' }}</td>
                                                             </tr>
                                                         @endforeach
                                                     </tbody>
                                                 </table>
                                             </div>
                                         </div>
                                     @endforeach
                                 </div>
                             @endforeach
                         @elseif ($semestreActivo)
                            <p class="text-gray-500 text-sm">No hay registros de asistencia para el semestre activo ({{ $semestreActivo->nombre }}).</p>
                         @else
                            <p class="text-red-500 text-sm">No se encontró un semestre activo.</p>
                         @endif
                    </div>

                    {{-- Contenido Pestaña Aulas Disponibles --}}
                    <div x-show="activeTab === 'aulas'">
                        <h4 class="mb-4 font-medium text-sm sm:text-md">Verificar Disponibilidad de Aulas</h4>
                        <form method="GET" action="{{ route('dashboard') }}" class="p-3 sm:p-4 mb-4 sm:mb-6 bg-gray-100 rounded-lg shadow-sm">
                            <input type="hidden" name="tab" value="aulas">
                            <div class="grid items-end grid-cols-1 gap-3 sm:gap-4 sm:grid-cols-3">
                                <div>
                                    <x-input-label for="check_date" :value="__('Seleccionar Fecha')" />
                                    <x-text-input id="check_date" class="block w-full mt-1 text-sm" type="date" name="check_date" :value="request('check_date', now()->toDateString())" required />
                                </div>
                                <div>
                                    <x-input-label for="check_time" :value="__('Seleccionar Hora')" />
                                    <x-text-input id="check_time" class="block w-full mt-1 text-sm" type="time" name="check_time" :value="request('check_time', now()->format('H:i'))" required />
                                </div>
                                <div>
                                    <x-primary-button class="w-full sm:w-auto text-xs sm:text-sm">Verificar Disponibilidad</x-primary-button>
                                </div>
                            </div>
                        </form>

                        @isset($aulasDisponibles)
                            <h5 class="mb-3 text-base sm:text-lg font-semibold">
                                Aulas Disponibles para {{ \Carbon\Carbon::parse(request('check_date'))->locale('es')->isoFormat('dddd D [de] MMMM') }} a las {{ date('H:i', strtotime(request('check_time'))) }}
                            </h5>
                            @if($aulasDisponibles->isEmpty())
                                <p class="text-gray-500 text-sm">No hay aulas disponibles en la fecha y hora seleccionada.</p>
                            @else
                                <div class="overflow-x-auto -mx-4 sm:mx-0 mb-4">
                                    <table class="min-w-full text-xs sm:text-sm divide-y divide-gray-200">
                                        <thead class="bg-green-50">
                                            <tr>
                                                <th class="px-2 sm:px-4 py-2 font-medium tracking-wider text-left text-gray-700 uppercase text-xs">Nombre</th>
                                                <th class="px-2 sm:px-4 py-2 font-medium tracking-wider text-left text-gray-700 uppercase text-xs">Piso</th>
                                                <th class="px-2 sm:px-4 py-2 font-medium tracking-wider text-left text-gray-700 uppercase text-xs hidden sm:table-cell">Tipo</th>
                                                <th class="px-2 sm:px-4 py-2 font-medium tracking-wider text-left text-gray-700 uppercase text-xs">Capacidad</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($aulasDisponibles as $aula)
                                                <tr>
                                                    <td class="px-2 sm:px-4 py-2 whitespace-nowrap text-xs sm:text-sm">{{ $aula->nombre }}</td>
                                                    <td class="px-2 sm:px-4 py-2 whitespace-nowrap text-xs sm:text-sm">{{ $aula->piso }}</td>
                                                    <td class="px-2 sm:px-4 py-2 whitespace-nowrap text-xs sm:text-sm hidden sm:table-cell">{{ $aula->tipo ?? '-' }}</td>
                                                    <td class="px-2 sm:px-4 py-2 whitespace-nowrap text-xs sm:text-sm">{{ $aula->capacidad ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            @if(isset($aulasOcupadas) && !$aulasOcupadas->isEmpty())
                                <h5 class="mt-6 mb-3 text-base sm:text-lg font-semibold text-red-700">Aulas Ocupadas</h5>
                                <div class="overflow-x-auto -mx-4 sm:mx-0">
                                    <table class="min-w-full text-xs sm:text-sm divide-y divide-gray-200">
                                         <thead class="bg-red-50">
                                            <tr>
                                                <th class="px-2 sm:px-4 py-2 font-medium tracking-wider text-left text-gray-700 uppercase text-xs">Nombre</th>
                                                <th class="px-2 sm:px-4 py-2 font-medium tracking-wider text-left text-gray-700 uppercase text-xs">Ocupada por</th>
                                                <th class="px-2 sm:px-4 py-2 font-medium tracking-wider text-left text-gray-700 uppercase text-xs">Hasta</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($aulasOcupadas as $horario)
                                                <tr>
                                                    <td class="px-2 sm:px-4 py-2 whitespace-nowrap text-xs sm:text-sm">{{ $horario->aula->nombre }} <span class="text-gray-500">(P.{{$horario->aula->piso}})</span></td>
                                                    <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm">
                                                        <span class="font-medium">{{ $horario->grupo->materia->sigla }}</span> - Gpo {{ $horario->grupo->nombre }}
                                                        <span class="hidden sm:inline text-gray-600">({{ $horario->grupo->docente->user->name }})</span>
                                                    </td>
                                                    <td class="px-2 sm:px-4 py-2 whitespace-nowrap text-xs sm:text-sm">{{ date('H:i', strtotime($horario->hora_fin)) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        @else
                           {{-- No muestra nada si no se ha hecho clic en Verificar --}}
                        @endisset
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
