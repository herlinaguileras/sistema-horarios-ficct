<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Editar Horario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Errores de validación --}}
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Formulario --}}
                    <form method="POST" action="{{ route('horarios.update', $horario) }}">
                        @csrf
                        @method('PUT')

                        {{-- Seleccionar Grupo --}}
                        <div class="mb-6">
                            <label for="grupo_id" class="block mb-2 text-sm font-medium text-gray-700">
                                Grupo (Carga Horaria) <span class="text-red-500">*</span>
                            </label>
                            <select name="grupo_id" id="grupo_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach($grupos as $grupo)
                                    <option value="{{ $grupo->id }}" {{ (old('grupo_id', $horario->grupo_id) == $grupo->id) ? 'selected' : '' }}>
                                        {{ $grupo->semestre->nombre }} | {{ $grupo->materia->sigla }} - {{ $grupo->materia->nombre }} | Grupo {{ $grupo->nombre }} | {{ $grupo->docente->user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('grupo_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Día de la semana --}}
                        <div class="mb-6">
                            <label for="dia_semana" class="block mb-2 text-sm font-medium text-gray-700">
                                Día de la Semana <span class="text-red-500">*</span>
                            </label>
                            <select name="dia_semana" id="dia_semana" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                                @foreach($dias as $numero => $nombre)
                                    <option value="{{ $numero }}" {{ (old('dia_semana', $horario->dia_semana) == $numero) ? 'selected' : '' }}>
                                        {{ $nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dia_semana')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Horas --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="hora_inicio" class="block mb-2 text-sm font-medium text-gray-700">
                                    Hora Inicio <span class="text-red-500">*</span>
                                </label>
                                <input type="time" 
                                       name="hora_inicio" 
                                       id="hora_inicio" 
                                       value="{{ old('hora_inicio', date('H:i', strtotime($horario->hora_inicio))) }}"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('hora_inicio')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="hora_fin" class="block mb-2 text-sm font-medium text-gray-700">
                                    Hora Fin <span class="text-red-500">*</span>
                                </label>
                                <input type="time" 
                                       name="hora_fin" 
                                       id="hora_fin" 
                                       value="{{ old('hora_fin', date('H:i', strtotime($horario->hora_fin))) }}"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('hora_fin')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Aula --}}
                        <div class="mb-6">
                            <label for="aula_id" class="block mb-2 text-sm font-medium text-gray-700">
                                Aula <span class="text-red-500">*</span>
                            </label>
                            <select name="aula_id" id="aula_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach($aulas as $aula)
                                    <option value="{{ $aula->id }}" {{ (old('aula_id', $horario->aula_id) == $aula->id) ? 'selected' : '' }}>
                                        {{ $aula->nombre }} - Piso {{ $aula->piso }} - {{ $aula->tipo }} (Cap: {{ $aula->capacidad }})
                                    </option>
                                @endforeach
                            </select>
                            @error('aula_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-end gap-4 pt-4 border-t">
                            <a href="{{ route('horarios.index') }}"
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                💾 Actualizar Horario
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
