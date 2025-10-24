<x-app-layout>
    {{-- Esta es la cabecera que aparece arriba (como la de "Dashboard") --}}
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Gestión de Docentes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-end mb-4">
        <a href="{{ route('docentes.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-gray-800 border border-transparent rounded-md hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25">
            Registrar Nuevo Docente
        </a>
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
                            <tbody class="bg-white divide-y divide-gray-200">
                                {{-- 3. Recorremos cada docente y mostramos sus datos --}}
                                @foreach ($docentes as $docente)
                                    <tr>
                                        {{-- Gracias a la optimización, podemos acceder a 'user' fácilmente --}}
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $docente->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $docente->user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $docente->codigo_docente }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $docente->estado }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                            <a href="{{ route('docentes.edit', $docente) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
