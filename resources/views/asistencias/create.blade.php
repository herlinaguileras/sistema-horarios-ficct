<x-app-layout>
    <x-slot name="header">
        {{-- Título Principal: Indica para qué clase es --}}
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
    Registrar Asistencia Manual: {{ $horario->grupo->materia->sigla }} - Gpo {{ $horario->grupo->nombre }}
     (@php $nombresDias = [ 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo']; @endphp {{ $nombresDias[$horario->dia_semana] ?? 'Día inválido' }} {{ date('H:i', strtotime($horario->hora_inicio)) }} - {{ date('H:i', strtotime($horario->hora_fin)) }})
</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8"> {{-- Max width slightly smaller --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Formulario --}}
                    {{-- Apunta a la ruta 'horarios.asistencias.store' --}}
                    <form method="POST" action="{{ route('horarios.asistencias.store', $horario) }}">
                        @csrf

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                            <div>
                                <x-input-label for="fecha" :value="__('Fecha de la Clase')" />
                                {{-- Pre-rellena con la fecha de hoy --}}
                                <x-text-input id="fecha" class="block w-full mt-1" type="date" name="fecha" :value="old('fecha', now()->toDateString())" required />
                                <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="hora_registro" :value="__('Hora de Registro')" />
                                {{-- Pre-rellena con la hora actual --}}
                                <x-text-input id="hora_registro" class="block w-full mt-1" type="time" name="hora_registro" :value="old('hora_registro', now()->format('H:i'))" required />
                                <x-input-error :messages="$errors->get('hora_registro')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2"> {{-- Ocupa las dos columnas --}}
                                <x-input-label for="estado" :value="__('Estado')" />
                                <select id="estado" name="estado" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="Presente">Presente</option>
                                    <option value="Ausente">Ausente</option>
                                    <option value="Licencia">Licencia</option>
                                </select>


                                <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                            </div>

                            {{-- Justificación (Obligatorio) --}}
    <div class="md:col-span-2"> {{-- Ocupa las dos columnas --}}
        <x-input-label for="justificacion" :value="__('Justificación (Obligatoria)')" />
        <textarea id="justificacion" name="justificacion" rows="3" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('justificacion') }}</textarea>
        <x-input-error :messages="$errors->get('justificacion')" class="mt-2" />
    </div>

                            {{-- Campo oculto para el método de registro --}}
                            <input type="hidden" name="metodo_registro" value="Manual">

                        </div>

                        <div class="flex items-center justify-end mt-6">
                            {{-- Botón Cancelar --}}
                            <a href="{{ route('horarios.asistencias.index', $horario) }}" class="mr-4 text-gray-600 hover:text-gray-900">
                                Cancelar
                            </a>
                            <x-primary-button>
                                {{ __('Guardar Asistencia') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
