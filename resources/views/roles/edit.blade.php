<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Rol: ') . $role->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (in_array($role->name, ['admin', 'docente']))
                        <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                            <strong>Advertencia:</strong> Este es un rol del sistema. El nombre no se puede modificar.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('roles.update', $role) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            {{-- Nombre del Rol --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre del Rol <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       value="{{ old('name', $role->name) }}"
                                       required
                                       {{ in_array($role->name, ['admin', 'docente']) ? 'readonly' : '' }}
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror {{ in_array($role->name, ['admin', 'docente']) ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @if (!in_array($role->name, ['admin', 'docente']))
                                    <p class="mt-1 text-xs text-gray-500">Usa minúsculas sin espacios (ej: supervisor, coordinador)</p>
                                @else
                                    <p class="mt-1 text-xs text-amber-600">⚠️ Rol del sistema, no se puede cambiar el nombre</p>
                                @endif
                            </div>

                            {{-- Nivel --}}
                            <div>
                                <label for="level" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nivel <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       name="level"
                                       id="level"
                                       value="{{ old('level', $role->level) }}"
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
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción
                            </label>
                            <textarea name="description"
                                      id="description"
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                                      placeholder="Describe las responsabilidades de este rol...">{{ old('description', $role->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Estado --}}
                        <div class="mb-6">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Estado <span class="text-red-500">*</span>
                            </label>
                            <select name="status"
                                    id="status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror"
                                    required>
                                <option value="Activo" {{ old('status', $role->status) === 'Activo' ? 'selected' : '' }}>Activo</option>
                                <option value="Inactivo" {{ old('status', $role->status) === 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Módulos del Rol --}}
                        <div class="mb-6">
                            <label class="block mb-3 text-sm font-medium text-gray-700">
                                Módulos del Sistema <span class="text-red-500">*</span>
                            </label>
                            <p class="mb-3 text-sm text-gray-600">
                                💡 <strong>Importante:</strong> Selecciona los módulos a los que este rol tendrá acceso completo (ver, crear, editar, eliminar).
                            </p>
                            <div class="p-4 border border-gray-300 rounded-md bg-gray-50">
                                @php
                                    $iconos = [
                                        'usuarios' => '👥',
                                        'roles' => '🛡️',
                                        'docentes' => '👨‍🏫',
                                        'materias' => '📚',
                                        'aulas' => '🏫',
                                        'grupos' => '👥',
                                        'semestres' => '📅',
                                        'horarios' => '🕐',
                                        'importacion' => '📤',
                                        'estadisticas' => '�',
                                    ];
                                    $colores = [
                                        'usuarios' => 'bg-pink-50 border-pink-200',
                                        'roles' => 'bg-gray-50 border-gray-200',
                                        'docentes' => 'bg-blue-50 border-blue-200',
                                        'materias' => 'bg-green-50 border-green-200',
                                        'aulas' => 'bg-red-50 border-red-200',
                                        'grupos' => 'bg-yellow-50 border-yellow-200',
                                        'semestres' => 'bg-teal-50 border-teal-200',
                                        'horarios' => 'bg-indigo-50 border-indigo-200',
                                        'importacion' => 'bg-cyan-50 border-cyan-200',
                                        'estadisticas' => 'bg-purple-50 border-purple-200',
                                    ];
                                    $selectedModules = old('modules', $role->modules->pluck('module_name')->toArray());
                                @endphp

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                    @foreach($modules as $moduleKey => $moduleInfo)
                                        <label class="flex items-start p-4 space-x-3 border-2 rounded-lg cursor-pointer transition-all {{ $colores[$moduleKey] ?? 'bg-gray-50 border-gray-200' }} hover:shadow-md {{ in_array($moduleKey, $selectedModules) ? 'ring-2 ring-blue-400' : '' }}">
                                            <input type="checkbox"
                                                   name="modules[]"
                                                   value="{{ $moduleKey }}"
                                                   class="w-5 h-5 mt-1 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                   {{ in_array($moduleKey, $selectedModules) ? 'checked' : '' }}>
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2 mb-1">
                                                    <span class="text-2xl">{{ $iconos[$moduleKey] ?? '📦' }}</span>
                                                    <span class="font-bold text-gray-800">{{ $moduleInfo['name'] }}</span>
                                                </div>
                                                <p class="text-xs text-gray-600">{{ $moduleInfo['description'] }}</p>
                                                <div class="mt-2 flex flex-wrap gap-1">
                                                    <span class="px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded">👁️ Ver</span>
                                                    <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-700 rounded">➕ Crear</span>
                                                    <span class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-700 rounded">✏️ Editar</span>
                                                    <span class="px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded">🗑️ Eliminar</span>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            @error('modules')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>                        {{-- Información de usuarios --}}
                        @if($role->users->count() > 0)
                            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="font-semibold text-blue-900 mb-2">
                                    Usuarios con este rol ({{ $role->users->count() }}):
                                </h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($role->users->take(10) as $user)
                                        <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">
                                            {{ $user->name }}
                                        </span>
                                    @endforeach
                                    @if($role->users->count() > 10)
                                        <span class="px-3 py-1 text-sm text-blue-600">
                                            +{{ $role->users->count() - 10 }} más
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Botones --}}
                        <div class="flex items-center justify-end gap-4 pt-4 border-t">
                            <a href="{{ route('roles.index') }}"
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                💾 Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
