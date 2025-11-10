<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Editar Usuario') }}: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        {{-- Datos de Usuario --}}
                        <div class="mb-6">
                            <h3 class="mb-4 text-lg font-semibold">Datos de Usuario</h3>

                            {{-- Nombre --}}
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nombre Completo *</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                                @if(auth()->user()->hasRole('admin'))
                                    {{-- Si el usuario actual es admin, puede editar el email --}}
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @else
                                    {{-- Si NO es admin, el email está bloqueado --}}
                                    <input type="email" id="email" value="{{ $user->email }}" disabled
                                           class="block w-full mt-1 bg-gray-100 border-gray-300 rounded-md shadow-sm cursor-not-allowed">
                                    <input type="hidden" name="email" value="{{ $user->email }}">
                                    <p class="mt-1 text-xs text-gray-500">El correo electrónico no puede ser modificado</p>
                                @endif
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Password (opcional al editar) --}}
                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700">Nueva Contraseña (opcional)</label>
                                <input type="password" name="password" id="password"
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-xs text-gray-500">Deja en blanco si no deseas cambiar la contraseña</p>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Confirmar Password --}}
                            <div class="mb-4">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            {{-- Rol --}}
                            <div class="mb-4">
                                @if($user->hasRole('docente') && $user->docente)
                                    {{-- Si es docente con perfil, mostrar solo lectura --}}
                                    <label class="block mb-2 text-sm font-medium text-gray-700">Rol</label>
                                    <div class="p-2 mt-2 rounded bg-blue-50">
                                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold text-blue-800 bg-blue-100 rounded-full">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                                            </svg>
                                            Docente
                                        </span>
                                    </div>
                                @else
                                    {{-- Si NO es docente, permitir cambiar rol --}}
                                    <label class="block mb-2 text-sm font-medium text-gray-700">Rol * (selecciona uno)</label>
                                    <div class="space-y-2">
                                        @foreach ($roles as $role)
                                            <label class="flex items-center">
                                                <input type="radio" name="role" value="{{ $role->id }}"
                                                       {{ old('role', $user->roles->first()?->id) == $role->id ? 'checked' : '' }}
                                                       class="text-indigo-600 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                                @endif
                            </div>
                        </div>

                        {{-- Datos de Docente (Solo para nuevos docentes) --}}
                        @if(!($user->hasRole('docente') && $user->docente))
                            <div id="docente-fields" class="hidden p-4 mb-6 rounded bg-gray-50">
                                <h3 class="mb-4 text-lg font-semibold">Datos de Docente</h3>

                                <div class="mb-4">
                                    <label for="codigo_docente" class="block text-sm font-medium text-gray-700">Código Docente</label>
                                    <input type="text" name="codigo_docente" id="codigo_docente"
                                           value="{{ old('codigo_docente', $user->docente?->codigo_docente) }}"
                                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('codigo_docente')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="carnet_identidad" class="block text-sm font-medium text-gray-700">Carnet de Identidad</label>
                                    <input type="text" name="carnet_identidad" id="carnet_identidad"
                                           value="{{ old('carnet_identidad', $user->docente?->carnet_identidad) }}"
                                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('carnet_identidad')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                    <input type="text" name="telefono" id="telefono"
                                           value="{{ old('telefono', $user->docente?->telefono) }}"
                                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('telefono')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="titulo" class="block text-sm font-medium text-gray-700">Título Académico</label>
                                    <input type="text" name="titulo" id="titulo"
                                           value="{{ old('titulo', $user->docente?->titulos?->first()?->nombre) }}"
                                           placeholder="Ej: Ingeniero de Sistemas"
                                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('titulo')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                @if($user->docente)
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                                        <p class="mt-1 text-sm">
                                            <span class="px-2 py-1 rounded-full {{ $user->docente->estado === 'Activo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $user->docente->estado }}
                                            </span>
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Botones --}}
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('users.index') }}"
                               class="px-4 py-2 font-bold text-gray-800 bg-gray-300 rounded hover:bg-gray-400">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                                Actualizar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript para mostrar/ocultar campos de docente --}}
    @if(!($user->hasRole('docente') && $user->docente))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleRadios = document.querySelectorAll('input[name="role"]');
            const docenteFields = document.getElementById('docente-fields');
            const docenteRoleId = document.getElementById('role-docente')?.value;

            if (roleRadios.length && docenteFields && docenteRoleId) {
                // Función para verificar si está seleccionado el rol docente
                function checkDocenteRole() {
                    const selectedRole = document.querySelector('input[name="role"]:checked');
                    if (selectedRole && selectedRole.value === docenteRoleId) {
                        docenteFields.classList.remove('hidden');
                    } else {
                        docenteFields.classList.add('hidden');
                    }
                }

                // Verificar al cargar la página
                checkDocenteRole();

                // Verificar al cambiar
                roleRadios.forEach(radio => {
                    radio.addEventListener('change', checkDocenteRole);
                });
            }
        });
    </script>
    @endif
</x-app-layout>
