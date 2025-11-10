<x-app-layout>
    {{-- Esta es la cabecera que aparece arriba (como la de "Dashboard") --}}
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Gestión de Docentes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Mensaje de éxito --}}
            @if (session('status'))
                <div class="mb-4 overflow-hidden bg-green-100 border border-green-400 rounded shadow-sm">
                    <div class="px-4 py-3 text-green-700">
                        {{ session('status') }}
                    </div>
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('docentes.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-gray-800 border border-transparent rounded-md hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25">
                            Registrar Nuevo Docente
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
                                   placeholder="Buscar por nombre, email o código docente..."
                                   autocomplete="off">
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            <span id="resultCount">{{ $docentes->count() }}</span> docente(s) encontrado(s)
                        </p>
                    </div>

                    {{-- 1. Revisamos si la variable $docentes está vacía --}}
                    @if($docentes->isEmpty())
                        <p>No hay docentes registrados todavía.</p>
                    @else
                        {{-- 2. Si no está vacía, mostramos una tabla --}}
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Email</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Código Docente</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Estado</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="docenteTableBody">
                                {{-- 3. Recorremos cada docente y mostramos sus datos --}}
                                @foreach ($docentes as $docente)
                                    <tr class="docente-row"
                                        data-name="{{ strtolower($docente->user->name) }}"
                                        data-email="{{ strtolower($docente->user->email) }}"
                                        data-codigo="{{ strtolower($docente->codigo_docente) }}"
                                        data-estado="{{ strtolower($docente->estado) }}">
                                        {{-- Gracias a la optimización, podemos acceder a 'user' fácilmente --}}
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $docente->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $docente->user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $docente->codigo_docente }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $docente->estado }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('docentes.edit', $docente) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                                <form method="POST" action="{{ route('docentes.destroy', $docente) }}" 
                                                      onsubmit="return confirm('¿Estás seguro de eliminar este docente? Esta acción también eliminará su cuenta de usuario.');" 
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr id="noResults" class="hidden">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No se encontraron docentes que coincidan con la búsqueda.
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
            const docenteRows = document.querySelectorAll('.docente-row');
            const noResults = document.getElementById('noResults');
            const resultCount = document.getElementById('resultCount');
            const totalDocentes = {{ $docentes->count() }};

            if (searchInput && docenteRows.length > 0) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    let visibleCount = 0;

                    if (searchTerm === '') {
                        docenteRows.forEach(row => {
                            row.style.display = '';
                        });
                        noResults.classList.add('hidden');
                        resultCount.textContent = totalDocentes;
                    } else {
                        docenteRows.forEach(row => {
                            const name = row.getAttribute('data-name');
                            const email = row.getAttribute('data-email');
                            const codigo = row.getAttribute('data-codigo');
                            const estado = row.getAttribute('data-estado');

                            const matches = name.includes(searchTerm) || 
                                          email.includes(searchTerm) || 
                                          codigo.includes(searchTerm) ||
                                          estado.includes(searchTerm);

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
