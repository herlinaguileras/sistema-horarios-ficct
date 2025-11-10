<h4 class="mb-4 font-medium text-md">Verificar Disponibilidad de Aulas</h4>
<form method="GET" action="{{ route('dashboard') }}" class="p-4 mb-6 bg-gray-100 rounded-lg shadow-sm">
    <input type="hidden" name="tab" value="aulas">
    <div class="grid items-end grid-cols-1 gap-4 sm:grid-cols-3">
        <div>
            <x-input-label for="check_date" :value="__('Seleccionar Fecha')" />
            <x-text-input id="check_date" class="block w-full mt-1" type="date" name="check_date" :value="request('check_date', now()->toDateString())" required />
        </div>
        <div>
            <x-input-label for="check_time" :value="__('Seleccionar Hora')" />
            <x-text-input id="check_time" class="block w-full mt-1" type="time" name="check_time" :value="request('check_time', now()->format('H:i'))" required />
        </div>
        <div><x-primary-button>Verificar Disponibilidad</x-primary-button></div>
    </div>
</form>

@isset($aulasDisponibles)
    <h5 class="mb-3 text-lg font-semibold">
        Aulas Disponibles para {{ \Carbon\Carbon::parse(request('check_date'))->locale('es')->isoFormat('dddd D [de] MMMM') }} a las {{ date('H:i', strtotime(request('check_time'))) }}
    </h5>
    @if($aulasDisponibles->isEmpty())
        <p class="text-gray-500">No hay aulas disponibles en la fecha y hora seleccionada.</p>
    @else
        <table class="min-w-full mb-4 text-sm divide-y divide-gray-200">
            <thead class="bg-green-50">
                <tr>
                    <th class="px-4 py-2 font-medium tracking-wider text-left text-gray-700 uppercase">Nombre</th>
                    <th class="px-4 py-2 font-medium tracking-wider text-left text-gray-700 uppercase">Piso</th>
                    <th class="px-4 py-2 font-medium tracking-wider text-left text-gray-700 uppercase">Tipo</th>
                    <th class="px-4 py-2 font-medium tracking-wider text-left text-gray-700 uppercase">Capacidad</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($aulasDisponibles as $aula)
                    <tr>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $aula->nombre }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $aula->piso }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $aula->tipo ?? '-' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $aula->capacidad ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if(isset($aulasOcupadas) && !$aulasOcupadas->isEmpty())
        <h5 class="mt-6 mb-3 text-lg font-semibold text-red-700">Aulas Ocupadas</h5>
        <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-red-50">
                <tr>
                    <th class="px-4 py-2 font-medium tracking-wider text-left text-gray-700 uppercase">Nombre</th>
                    <th class="px-4 py-2 font-medium tracking-wider text-left text-gray-700 uppercase">Ocupada por</th>
                    <th class="px-4 py-2 font-medium tracking-wider text-left text-gray-700 uppercase">Hasta</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($aulasOcupadas as $horario)
                    <tr>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $horario->aula->nombre }} (P.{{$horario->aula->piso}})</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $horario->grupo->materia->sigla }} - Gpo {{ $horario->grupo->nombre }} ({{ $horario->grupo->docente->user->name }})</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ date('H:i', strtotime($horario->hora_fin)) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@else
    {{-- No muestra nada si no se ha hecho clic en Verificar --}}
@endisset
