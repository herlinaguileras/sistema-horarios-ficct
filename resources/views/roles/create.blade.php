<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Crear Nuevo Rol') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('roles.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
                            {{-- Nombre del Rol --}}
                            <div>
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-700">
                                    Nombre del Rol <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       value="{{ old('name') }}"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                                       placeholder="ej: coordinador, secretaria">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Usa minúsculas sin espacios (ej: supervisor, coordinador)</p>
                            </div>

                            {{-- Nivel --}}
                            <div>
                                <label for="level" class="block mb-2 text-sm font-medium text-gray-700">
                                    Nivel <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       name="level"
                                       id="level"
                                       value="{{ old('level', 10) }}"
                                       min="1"
                                       max="100"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('level') border-red-500 @enderror">
                                @error('level')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Mayor nivel = mayor jerarquía (admin=100)</p>
                            </div>
                        </div>

                        {{-- Descripción --}}
                        <div class="mb-6">
                            <label for="description" class="block mb-2 text-sm font-medium text-gray-700">
                                Descripción
                            </label>
                            <textarea name="description"
                                      id="description"
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                                      placeholder="Describe las responsabilidades de este rol...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Estado --}}
                        <div class="mb-6">
                            <label for="status" class="block mb-2 text-sm font-medium text-gray-700">
                                Estado <span class="text-red-500">*</span>
                            </label>
                            <select name="status"
                                    id="status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror"
                                    required>
                                <option value="Activo" {{ old('status', 'Activo') === 'Activo' ? 'selected' : '' }}>Activo</option>
                                <option value="Inactivo" {{ old('status') === 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Permisos del Rol --}}
                        <div class="mb-6">
                            <label class="block mb-3 text-sm font-medium text-gray-700">
                                Permisos del Rol
                            </label>
                            <div class="p-4 border border-gray-300 rounded-md bg-gray-50">
                                @if($permissions->isEmpty())
                                    <p class="text-sm text-gray-500">No hay permisos disponibles. <a href="#" class="text-blue-600 hover:underline">Ejecuta el seeder de permisos</a>.</p>
                                @else
                                    {{-- Agrupar permisos por módulo --}}
                                    @foreach($permissions as $module => $modulePermissions)
                                        <div class="mb-4">
                                            <h4 class="mb-2 text-sm font-semibold text-gray-700">📦 {{ $module }}</h4>
                                            <div class="grid grid-cols-1 gap-2 pl-4 md:grid-cols-2 lg:grid-cols-3">
                                                @foreach($modulePermissions as $permission)
                                                    <label class="flex items-center space-x-2">
                                                        <input type="checkbox"
                                                               name="permissions[]"
                                                               value="{{ $permission->id }}"
                                                               {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                        <span class="text-sm text-gray-700">{{ $permission->description }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            @error('permissions')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-end gap-4 pt-4 border-t">
                            <a href="{{ route('roles.index') }}"
                               class="px-4 py-2 font-bold text-gray-800 bg-gray-300 rounded hover:bg-gray-400">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                                💾 Crear Rol
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
