<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Añadir Horario a: {{ $grupo->materia->sigla }} - Grupo {{ $grupo->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Formulario --}}
                    {{-- Apunta a la ruta 'grupos.horarios.store' --}}
                    <form method="POST" action="{{ route('grupos.horarios.store', $grupo) }}">
                        @csrf

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                            <div>
                                <x-input-label for="dia_semana" :value="__('Día de la Semana')" />
                               <select id="dia_semana" name="dia_semana" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
    <option value="1">Lunes</option>
    <option value="2">Martes</option>
    <option value="3">Miércoles</option>
    <option value="4">Jueves</option>
    <option value="5">Viernes</option>
    <option value="6">Sábado</option>
    {{-- Decide if you need Sunday (7) --}}
    {{-- <option value="7">Domingo</option> --}}
</select>
                                <x-input-error :messages="$errors->get('dia_semana')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="aula_id" :value="__('Aula')" />
                                <select id="aula_id" name="aula_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">-- Seleccione un aula --</option>
                                    @foreach($aulas as $aula)

                                        <option value="{{ $aula->id }}">
                                            {{ $aula->nombre }} (Piso {{ $aula->piso }}, Tipo: {{ $aula->tipo }})
                                        </option>
                                    @endforeach

                                </select>
                                <x-input-error :messages="$errors->get('aula_id')" class="mt-2" />
        <x-input-error :messages="$errors->get('docente_id')" class="mt-2" />
        <x-input-error :messages="$errors->get('grupo_id')" class="mt-2" />
                                <x-input-error :messages="$errors->get('aula_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="hora_inicio" :value="__('Hora de Inicio')" />
                                <x-text-input id="hora_inicio" class="block w-full mt-1" type="time" name="hora_inicio" :value="old('hora_inicio')" required />
                                <x-input-error :messages="$errors->get('hora_inicio')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="hora_fin" :value="__('Hora de Fin')" />
                                <x-text-input id="hora_fin" class="block w-full mt-1" type="time" name="hora_fin" :value="old('hora_fin')" required />
                                <x-input-error :messages="$errors->get('hora_fin')" class="mt-2" />
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('grupos.horarios.index', $grupo) }}" class="mr-4 text-gray-600 hover:text-gray-900">
                                Cancelar
                            </a>
                            <x-primary-button>
                                {{ __('Guardar Horario') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
