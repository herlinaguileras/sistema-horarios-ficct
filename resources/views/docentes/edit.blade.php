<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Editar Docente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('docentes.update', $docente) }}">
                        @method('PUT')
                        @csrf

                        <h3 class="mb-2 text-lg font-semibold">Datos de la Cuenta</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                            <div>
                                <x-input-label for="name" :value="__('Nombre Completo')" />
                                <x-text-input id="name" class="block w-full mt-1" type="text" name="name" :value="old('name', $docente->user->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block w-full mt-1" type="email" name="email" :value="old('email', $docente->user->email)" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            {{-- CAMPOS DE CONTRASEÑA ELIMINADOS DE AQUÍ --}}

                        </div>

                        <hr class="my-6">

                        <h3 class="mb-2 text-lg font-semibold">Datos del Perfil Profesional</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                            <div>
                                <x-input-label for="codigo_docente" :value="__('Código Docente')" />
                                <x-text-input id="codigo_docente" class="block w-full mt-1" type="text" name="codigo_docente" :value="old('codigo_docente', $docente->codigo_docente)" required />
                                <x-input-error :messages="$errors->get('codigo_docente')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="carnet_identidad" :value="__('Carnet de Identidad')" />
                                <x-text-input id="carnet_identidad" class="block w-full mt-1" type="text" name="carnet_identidad" :value="old('carnet_identidad', $docente->carnet_identidad)" required />
                                <x-input-error :messages="$errors->get('carnet_identidad')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="titulo" :value="__('Título Profesional')" />
                                <x-text-input id="titulo" class="block w-full mt-1" type="text" name="titulo" :value="old('titulo', $docente->titulos->first()->nombre ?? '')" required />
                                <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="telefono" :value="__('Teléfono (Opcional)')" />
                                <x-text-input id="telefono" class="block w-full mt-1" type="text" name="telefono" :value="old('telefono', $docente->telefono)" />
                                <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                            </div>
                        </div>

                        <hr class="my-6">

                        <h3 class="mb-2 text-lg font-semibold">Cambiar Contraseña (Opcional)</h3>
                        <p class="mb-4 text-sm text-gray-600">
                            Deja estos campos vacíos si no deseas cambiar la contraseña.
                        </p>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                             <div>
                                <x-input-label for="password" :value="__('Nueva Contraseña')" />
                                <x-text-input id="password" class="block w-full mt-1" type="password" name="password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirmar Nueva Contraseña')" />
                                <x-text-input id="password_confirmation" class="block w-full mt-1" type="password" name="password_confirmation" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>


                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Actualizar Docente') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
