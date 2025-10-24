<x-app-layout>
    {{-- Cabecera de la página --}}
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Editar Aula') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Formulario de Edición --}}
                    <form method="POST" action="{{ route('aulas.update', $aula) }}">
                        @method('PUT') {{-- Método para Actualizar --}}
                        @csrf        {{-- Token de seguridad (soluciona el error 419) --}}

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                            <div>
                                <x-input-label for="nombre" :value="__('Nombre del Aula (Ej: 225-01)')" />
                                <x-text-input id="nombre" class="block w-full mt-1" type="text" name="nombre" :value="old('nombre', $aula->nombre)" required autofocus />
                                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="piso" :value="__('Piso (Ej: 1, 2, 3... )')" />
                                <x-text-input id="piso" class="block w-full mt-1" type="number" name="piso" :value="old('piso', $aula->piso)" required />
                                <x-input-error :messages="$errors->get('piso')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="tipo" :value="__('Tipo de Aula')" />
                                <select id="tipo" name="tipo" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    {{-- Lógica para pre-seleccionar la opción guardada --}}
                                    <option value="Aula Común" @if($aula->tipo == 'Aula Común') selected @endif>Aula Común</option>
                                    <option value="Laboratorio" @if($aula->tipo == 'Laboratorio') selected @endif>Laboratorio</option>
                                    <option value="Auditorio" @if($aula->tipo == 'Auditorio') selected @endif>Auditorio</option>
                                </select>
                                <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="capacidad" :value="__('Capacidad (Opcional)')" />
                                <x-text-input id="capacidad" class="block w-full mt-1" type="number" name="capacidad" :value="old('capacidad', $aula->capacidad)" />
                                <x-input-error :messages="$errors->get('capacidad')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Actualizar Aula') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
