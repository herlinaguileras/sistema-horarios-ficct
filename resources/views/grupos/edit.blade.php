<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Editar Carga Horaria (Grupo)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Formulario --}}
                    <form method="POST" action="{{ route('grupos.update', $grupo) }}">
                        @method('PUT')
                        @csrf

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                            <div>
                                <x-input-label for="semestre_id" :value="__('Semestre')" />
                                <select id="semestre_id" name="semestre_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    @foreach($semestres as $semestre)
                                        <option value="{{ $semestre->id }}" @if($semestre->id == $grupo->semestre_id) selected @endif>
                                            {{ $semestre->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('semestre_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="materia_id" :value="__('Materia')" />
                                <select id="materia_id" name="materia_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    @foreach($materias as $materia)
                                        <option value="{{ $materia->id }}" @if($materia->id == $grupo->materia_id) selected @endif>
                                            {{ $materia->sigla }} - {{ $materia->nombre }} ({{ $materia->carrera }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('materia_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="docente_id" :value="__('Docente')" />
                                <select id="docente_id" name="docente_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    @foreach($docentes as $docente)
                                        <option value="{{ $docente->id }}" @if($docente->id == $grupo->docente_id) selected @endif>
                                            {{ $docente->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('docente_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="nombre" :value="__('Nombre del Grupo (Ej: SA, SB, SC...)')" />
                                <x-text-input id="nombre" class="block w-full mt-1" type="text" name="nombre" :value="old('nombre', $grupo->nombre)" required />
                                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Actualizar Grupo') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
