<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Roles') }}
            </h2>
            <a href="{{ route('roles.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                + Nuevo Rol
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensajes de estado --}}
            @if (session('status'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Barra de búsqueda en tiempo real --}}
            <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" 
                           id="searchInput" 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                           placeholder="Buscar por nombre de rol, descripción o nivel..."
                           autocomplete="off">
                </div>
                <p class="mt-2 text-xs text-gray-500">
                    <span id="resultCount">{{ $roles->count() }}</span> rol(es) encontrado(s)
                </p>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nombre Rol
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Descripción
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nivel
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Usuarios
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Permisos
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="roleTableBody">
                                @forelse ($roles as $role)
                                    <tr class="role-row"
                                        data-name="{{ strtolower($role->name) }}"
                                        data-description="{{ strtolower($role->description ?? '') }}"
                                        data-level="{{ $role->level }}"
                                        data-status="{{ strtolower($role->status) }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $role->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-500">
                                                {{ $role->description ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                                Nivel {{ $role->level }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($role->status === 'Activo')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Activo
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                @if($role->users_count > 0)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                        {{ $role->users_count }} usuario(s)
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">Sin usuarios</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                @if($role->permissions_count > 0)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        {{ $role->permissions_count }} permiso(s)
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">Sin permisos</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex gap-3 items-center">
                                                {{-- Botón Toggle Estado --}}
                                                <form action="{{ route('roles.toggle-status', $role) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="px-3 py-1 text-xs font-semibold rounded-md transition-colors {{ $role->status === 'Activo' ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                                                        @if($role->status === 'Activo')
                                                            Desactivar
                                                        @else
                                                            Activar
                                                        @endif
                                                    </button>
                                                </form>

                                                {{-- Botón Editar --}}
                                                <a href="{{ route('roles.edit', $role) }}"
                                                   class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                                    ✏️ Editar
                                                </a>

                                                {{-- Botón Eliminar (solo si no es del sistema) --}}
                                                @if (!in_array($role->name, ['admin', 'docente']))
                                                    <form action="{{ route('roles.destroy', $role) }}" method="POST"
                                                          class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este rol? Esta acción no se puede deshacer.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="text-red-600 hover:text-red-900 font-semibold">
                                                            🗑️ Eliminar
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-400 text-xs italic">Rol del sistema</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="emptyRow">
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            No hay roles registrados.
                                        </td>
                                    </tr>
                                @endforelse
                                <tr id="noResults" class="hidden">
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No se encontraron roles que coincidan con la búsqueda.
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="mt-4 text-sm text-gray-600">
                            <p><strong>Nota:</strong> Los roles "admin" y "docente" son del sistema y no se pueden eliminar.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script de búsqueda en tiempo real --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const roleRows = document.querySelectorAll('.role-row');
            const emptyRow = document.getElementById('emptyRow');
            const noResults = document.getElementById('noResults');
            const resultCount = document.getElementById('resultCount');
            const totalRoles = {{ $roles->count() }};

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                let visibleCount = 0;

                if (searchTerm === '') {
                    roleRows.forEach(row => {
                        row.style.display = '';
                    });
                    if (emptyRow) emptyRow.style.display = '';
                    noResults.classList.add('hidden');
                    resultCount.textContent = totalRoles;
                } else {
                    if (emptyRow) emptyRow.style.display = 'none';

                    roleRows.forEach(row => {
                        const name = row.getAttribute('data-name');
                        const description = row.getAttribute('data-description');
                        const level = row.getAttribute('data-level');
                        const status = row.getAttribute('data-status');

                        const matches = name.includes(searchTerm) || 
                                      description.includes(searchTerm) || 
                                      level.includes(searchTerm) ||
                                      status.includes(searchTerm);

                        if (matches) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    if (visibleCount === 0) {
                        noResults.classList.remove('hidden');
                    } else {
                        noResults.classList.add('hidden');
                    }

                    resultCount.textContent = visibleCount;
                }
            });

            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    this.value = '';
                    this.dispatchEvent(new Event('input'));
                    this.blur();
                }
            });
        });
    </script>
</x-app-layout>
