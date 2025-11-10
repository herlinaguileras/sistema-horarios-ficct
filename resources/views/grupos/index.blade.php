<x-app-layout>
    {{-- Cabecera de la página --}}
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Gestión de Grupos (Carga Horaria)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Mensaje de éxito --}}
                    @if (session('status'))
                        <div class="inline-block w-full p-4 mb-4 text-base text-green-700 bg-green-100 rounded-lg" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Botón para ir al formulario de creación --}}
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('grupos.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-gray-800 border border-transparent rounded-md hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25">
                            Asignar Carga Horaria (Grupo)
                        </a>
                    </div>

                    {{-- Barra de búsqueda en tiempo real --}}
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
                                   placeholder="Buscar por semestre, materia, grupo o docente..."
                                   autocomplete="off">
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            <span id="resultCount">{{ $grupos->count() }}</span> grupo(s) encontrado(s)
                        </p>
                    </div>

                    {{-- Tabla de Grupos --}}
                    @if($grupos->isEmpty())
                        <p>No hay grupos (carga horaria) registrados todavía.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Semestre</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Materia</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Grupo</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Docente</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="grupoTableBody">
                                @foreach ($grupos as $grupo)
                                    <tr class="grupo-row"
                                        data-semestre="{{ strtolower($grupo->semestre->nombre) }}"
                                        data-materia="{{ strtolower($grupo->materia->sigla . ' ' . $grupo->materia->nombre) }}"
                                        data-grupo="{{ strtolower($grupo->nombre) }}"
                                        data-docente="{{ strtolower($grupo->docente->user->name) }}">
                                        {{-- Usamos las relaciones que cargamos en el controlador --}}
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $grupo->semestre->nombre }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $grupo->materia->sigla }} - {{ $grupo->materia->nombre }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $grupo->nombre }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $grupo->docente->user->name }}</td>

                                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                            {{-- Botón Editar --}}
                                            <a href="{{ route('grupos.edit', $grupo) }}" class="mr-4 text-blue-600 hover:text-blue-900">
                                                Editar
                                            </a>
                                            
                                            {{-- Formulario de Eliminación --}}
                                            <form action="{{ route('grupos.destroy', $grupo) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro de eliminar este grupo?');">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr id="noResults" class="hidden">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No se encontraron grupos que coincidan con la búsqueda.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Script de búsqueda en tiempo real --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const grupoRows = document.querySelectorAll('.grupo-row');
            const noResults = document.getElementById('noResults');
            const resultCount = document.getElementById('resultCount');
            const totalGrupos = {{ $grupos->count() }};

            if (searchInput && grupoRows.length > 0) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    let visibleCount = 0;

                    if (searchTerm === '') {
                        grupoRows.forEach(row => {
                            row.style.display = '';
                        });
                        noResults.classList.add('hidden');
                        resultCount.textContent = totalGrupos;
                    } else {
                        grupoRows.forEach(row => {
                            const semestre = row.getAttribute('data-semestre');
                            const materia = row.getAttribute('data-materia');
                            const grupo = row.getAttribute('data-grupo');
                            const docente = row.getAttribute('data-docente');

                            const matches = semestre.includes(searchTerm) || 
                                          materia.includes(searchTerm) || 
                                          grupo.includes(searchTerm) ||
                                          docente.includes(searchTerm);

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
            }
        });
    </script>
</x-app-layout>
