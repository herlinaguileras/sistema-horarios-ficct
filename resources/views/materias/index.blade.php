<x-app-layout>
    {{-- Cabecera de la página --}}
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Gestión de Materias') }}
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

                {{-- Mensaje de error --}}
                @if ($errors->any())
                    <div class="inline-block w-full p-4 mb-4 text-base text-red-700 bg-red-100 rounded-lg" role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                    {{-- Botón para ir al formulario de creación --}}
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('materias.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-gray-800 border border-transparent rounded-md hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25">
                            Registrar Nueva Materia
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
                                   placeholder="Buscar por nombre, sigla, nivel o carrera..."
                                   autocomplete="off">
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            <span id="resultCount">{{ $materias->count() }}</span> materia(s) encontrada(s)
                        </p>
                    </div>

                    {{-- Tabla de Materias --}}
                    @if($materias->isEmpty())
                        <p>No hay materias registradas todavía.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Sigla</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nivel</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Carreras</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="materiaTableBody">
                                @foreach ($materias as $materia)
                                    <tr class="materia-row"
                                        data-nombre="{{ strtolower($materia->nombre) }}"
                                        data-sigla="{{ strtolower($materia->sigla) }}"
                                        data-nivel="{{ strtolower($materia->nivel_semestre) }}"
                                        data-carrera="{{ strtolower($materia->carreras->pluck('nombre')->implode(' ')) }}">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $materia->nombre }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $materia->sigla }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $materia->nivel_semestre }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @forelse($materia->carreras as $carrera)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                        @if($carrera->codigo === 'SIS') bg-red-100 text-red-800
                                                        @elseif($carrera->codigo === 'INF') bg-blue-100 text-blue-800
                                                        @elseif($carrera->codigo === 'RED') bg-orange-100 text-orange-800
                                                        @elseif($carrera->codigo === 'ROB') bg-purple-100 text-purple-800
                                                        @else bg-gray-100 text-gray-800
                                                        @endif">
                                                        {{ $carrera->codigo }}
                                                    </span>
                                                @empty
                                                    <span class="text-sm text-gray-400">Sin carreras</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="{{ route('materias.edit', $materia) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900">
                                                    Editar
                                                </a>
                                                
                                                <form action="{{ route('materias.destroy', $materia) }}" 
                                                      method="POST" 
                                                      class="inline-block"
                                                      onsubmit="return confirm('¿Estás seguro de que deseas eliminar la materia {{ $materia->nombre }}? Esta acción no se puede deshacer.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr id="noResults" class="hidden">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No se encontraron materias que coincidan con la búsqueda.
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
            const materiaRows = document.querySelectorAll('.materia-row');
            const noResults = document.getElementById('noResults');
            const resultCount = document.getElementById('resultCount');
            const totalMaterias = {{ $materias->count() }};

            if (searchInput && materiaRows.length > 0) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    let visibleCount = 0;

                    if (searchTerm === '') {
                        materiaRows.forEach(row => {
                            row.style.display = '';
                        });
                        noResults.classList.add('hidden');
                        resultCount.textContent = totalMaterias;
                    } else {
                        materiaRows.forEach(row => {
                            const nombre = row.getAttribute('data-nombre');
                            const sigla = row.getAttribute('data-sigla');
                            const nivel = row.getAttribute('data-nivel');
                            const carrera = row.getAttribute('data-carrera');

                            const matches = nombre.includes(searchTerm) ||
                                          sigla.includes(searchTerm) ||
                                          nivel.includes(searchTerm) ||
                                          carrera.includes(searchTerm);

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
