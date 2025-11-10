<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Registrar Nuevo Docente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Formulario --}}
                    {{-- AÚN NO HEMOS CREADO LA RUTA 'docentes.store', LO HAREMOS LUEGO --}}
                    <form method="POST" action="{{ route('docentes.store') }}">
                        @csrf

                       

                        <h3 class="mb-2 text-lg font-semibold">Datos de la Cuenta</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                            <div>
                                <x-input-label for="name" :value="__('Nombre Completo')" />
                                <x-text-input id="name" class="block w-full mt-1" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block w-full mt-1" type="email" name="email" :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password" :value="__('Contraseña')" />
                                <x-text-input id="password" class="block w-full mt-1" type="password" name="password" required />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
                                <x-text-input id="password_confirmation" class="block w-full mt-1" type="password" name="password_confirmation" required />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <hr class="my-6">

                        <h3 class="mb-2 text-lg font-semibold">Datos del Perfil Profesional</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                            <div>
                                <x-input-label for="codigo_docente" :value="__('Código Docente')" />
                                <x-text-input id="codigo_docente" 
                                              class="block w-full mt-1 bg-gray-100 cursor-not-allowed" 
                                              type="text" 
                                              name="codigo_docente_display" 
                                              :value="$proximoCodigo" 
                                              readonly />
                                <p class="mt-1 text-xs text-green-600">✓ Se asignará automáticamente al guardar</p>
                            </div>

                            <div>
                                <x-input-label for="carnet_identidad" :value="__('Carnet de Identidad')" />
                                <x-text-input id="carnet_identidad" class="block w-full mt-1" type="text" name="carnet_identidad" :value="old('carnet_identidad')" required />
                                <x-input-error :messages="$errors->get('carnet_identidad')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="titulo" :value="__('Título Profesional')" />
                                <x-text-input id="titulo" class="block w-full mt-1" type="text" name="titulo" :value="old('titulo')" required />
                                <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="telefono" :value="__('Teléfono (Opcional)')" />
                                <x-text-input id="telefono" class="block w-full mt-1" type="text" name="telefono" :value="old('telefono')" />
                                <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Guardar Docente') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
