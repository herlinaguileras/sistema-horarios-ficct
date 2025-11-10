<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nuevo Usuario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        {{-- Datos de Usuario --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Datos de Usuario</h3>

                            {{-- Nombre --}}
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nombre Completo *</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña *</label>
                                <input type="password" name="password" id="password" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Confirmar Password --}}
                            <div class="mb-4">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña *</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            {{-- Rol (solo uno) --}}
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rol * (selecciona uno)</label>
                                <div class="space-y-2">
                                    @foreach ($roles as $role)
                                        <label class="flex items-center">
                                            <input type="radio" name="role" value="{{ $role->id }}"
                                                   {{ old('role') == $role->id ? 'checked' : '' }}
                                                   class="border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                   {{ $role->name === 'docente' ? 'id=role-docente' : '' }}
                                                   required>
                                            <span class="ml-2 text-sm text-gray-700">
                                                {{ ucfirst($role->name) }}
                                                @if($role->description)
                                                    <span class="text-xs text-gray-500">({{ $role->description }})</span>
                                                @endif
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="mt-1 text-xs text-gray-500">⚠️ Cada usuario solo puede tener un rol para mantener la integridad del sistema</p>
                                @error('role')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Datos de Docente (solo si tiene rol docente) --}}
                        <div id="docente-fields" class="mb-6 p-4 bg-gray-50 rounded hidden">
                            <h3 class="text-lg font-semibold mb-4">Datos de Docente</h3>

                            <div class="mb-4">
                                <label for="codigo_docente" class="block text-sm font-medium text-gray-700">Código Docente</label>
                                <input type="text" name="codigo_docente" id="codigo_docente" value="{{ old('codigo_docente') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('codigo_docente')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="carnet_identidad" class="block text-sm font-medium text-gray-700">Carnet de Identidad</label>
                                <input type="text" name="carnet_identidad" id="carnet_identidad" value="{{ old('carnet_identidad') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('carnet_identidad')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('telefono')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="titulo" class="block text-sm font-medium text-gray-700">Título Académico</label>
                                <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}"
                                       placeholder="Ej: Ingeniero de Sistemas"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('titulo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <p class="text-sm text-gray-600 mt-2">
                                <strong>Nota:</strong> Si seleccionaste rol "Docente", debes completar al menos Código Docente y CI.
                            </p>
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('users.index') }}"
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Crear Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript para mostrar/ocultar campos de docente --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const docenteCheckbox = document.getElementById('role-docente');
            const docenteFields = document.getElementById('docente-fields');

            if (docenteCheckbox) {
                // Mostrar si ya está marcado (old input)
                if (docenteCheckbox.checked) {
                    docenteFields.classList.remove('hidden');
                }

                // Toggle al cambiar
                docenteCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        docenteFields.classList.remove('hidden');
                    } else {
                        docenteFields.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</x-app-layout>
