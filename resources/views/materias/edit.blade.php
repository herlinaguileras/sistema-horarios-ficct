<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Editar Materia') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Formulario --}}
                    <form method="POST" action="{{ route('materias.update', $materia) }}">
                        @method('PUT')
                        @csrf

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                            <div>
                                <x-input-label for="nombre" :value="__('Nombre de la Materia')" />
                                <x-text-input id="nombre" class="block w-full mt-1" type="text" name="nombre" :value="old('nombre', $materia->nombre)" required autofocus />
                                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="sigla" :value="__('Sigla de Materia (Ej: SIS123)')" />
                                <x-text-input id="sigla" class="block w-full mt-1" type="text" name="sigla" :value="old('sigla', $materia->sigla)" required />
                                <x-input-error :messages="$errors->get('sigla')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="nivel_semestre" :value="__('Nivel o Semestre (Ej: 1, 2, 3...)')" />
                                <x-text-input id="nivel_semestre" class="block w-full mt-1" type="number" name="nivel_semestre" :value="old('nivel_semestre', $materia->nivel_semestre)" required />
                                <x-input-error :messages="$errors->get('nivel_semestre')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="carrera" :value="__('Carrera')" />
                                <select id="carrera" name="carrera" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="Sistemas"    @if($materia->carrera == 'Sistemas') selected @endif>Ing. en Sistemas</option>
                                    <option value="Informatica" @if($materia->carrera == 'Informatica') selected @endif>Ing. Informática</option>
                                    <option value="Redes"       @if($materia->carrera == 'Redes') selected @endif>Ing. en Redes y Telecom.</option>
                                    <option value="Robotica"    @if($materia->carrera == 'Robotica') selected @endif>Ing. Robótica</option>
                                </select>
                                <x-input-error :messages="$errors->get('carrera')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Actualizar Materia') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
