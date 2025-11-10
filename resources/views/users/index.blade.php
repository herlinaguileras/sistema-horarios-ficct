<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Usuarios') }}
            </h2>
            <a href="{{ route('users.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                + Nuevo Usuario
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Barra de Búsqueda --}}
                    <div class="mb-6">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" 
                                   id="searchInput" 
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                   placeholder="Buscar por nombre, email, rol, código docente o CI..."
                                   autocomplete="off">
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            <span id="resultCount">{{ $users->total() }}</span> usuario(s) encontrado(s)
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nombre
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Roles
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Perfil Docente
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                                                        <tbody class="bg-white divide-y divide-gray-200" id="userTableBody">
                                @forelse ($users as $user)
                                    <tr class="user-row" 
                                        data-name="{{ strtolower($user->name) }}"
                                        data-email="{{ strtolower($user->email) }}"
                                        data-roles="{{ strtolower($user->roles->pluck('name')->implode(' ')) }}"
                                        data-codigo="{{ $user->docente ? strtolower($user->docente->codigo_docente) : '' }}"
                                        data-ci="{{ $user->docente && $user->docente->carnet_identidad ? strtolower($user->docente->carnet_identidad) : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $user->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($user->roles as $role)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        {{ ucfirst($role->name) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->docente)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ $user->docente->codigo_docente }}
                                                </span>
                                            @else
                                                <span class="text-sm text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->is_active)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Activo
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex gap-2">
                                                <a href="{{ route('users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    Editar
                                                </a>
                                                
                                                <form action="{{ route('users.toggle-estado', $user) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-yellow-600 hover:text-yellow-900">
                                                        {{ $user->is_active ? 'Desactivar' : 'Activar' }}
                                                    </button>
                                                </form>

                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" 
                                                      onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="emptyRow">
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No hay usuarios registrados.
                                        </td>
                                    </tr>
                                @endforelse
                                <tr id="noResults" class="hidden">
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No se encontraron usuarios que coincidan con la búsqueda.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    <div class="mt-4" id="paginationDiv">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script de búsqueda en tiempo real --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const userRows = document.querySelectorAll('.user-row');
            const emptyRow = document.getElementById('emptyRow');
            const noResults = document.getElementById('noResults');
            const resultCount = document.getElementById('resultCount');
            const paginationDiv = document.getElementById('paginationDiv');
            const totalUsers = {{ $users->total() }};

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                let visibleCount = 0;

                if (searchTerm === '') {
                    // Mostrar todos y restaurar paginación
                    userRows.forEach(row => {
                        row.style.display = '';
                    });
                    if (emptyRow) emptyRow.style.display = '';
                    noResults.classList.add('hidden');
                    paginationDiv.style.display = '';
                    resultCount.textContent = totalUsers;
                } else {
                    // Filtrar filas
                    if (emptyRow) emptyRow.style.display = 'none';
                    paginationDiv.style.display = 'none';

                    userRows.forEach(row => {
                        const name = row.getAttribute('data-name');
                        const email = row.getAttribute('data-email');
                        const roles = row.getAttribute('data-roles');
                        const codigo = row.getAttribute('data-codigo');
                        const ci = row.getAttribute('data-ci');

                        const matches = name.includes(searchTerm) || 
                                      email.includes(searchTerm) || 
                                      roles.includes(searchTerm) || 
                                      codigo.includes(searchTerm) || 
                                      ci.includes(searchTerm);

                        if (matches) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Mostrar/ocultar mensaje de sin resultados
                    if (visibleCount === 0) {
                        noResults.classList.remove('hidden');
                    } else {
                        noResults.classList.add('hidden');
                    }

                    resultCount.textContent = visibleCount;
                }
            });

            // Permitir borrar con ESC
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
