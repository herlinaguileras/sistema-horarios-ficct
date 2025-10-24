<x-app-layout>
    {{-- Cabecera de la página --}}
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Gestión de Aulas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Mensaje de éxito (para create, update, destroy) --}}
                    @if (session('status'))
                        <div class="inline-block w-full p-4 mb-4 text-base text-green-700 bg-green-100 rounded-lg" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Botón para ir al formulario de creación --}}
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('aulas.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-gray-800 border border-transparent rounded-md hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25">
                            Registrar Nueva Aula
                        </a>
                    </div>

                    {{-- Tabla de Aulas --}}
                    @if($aulas->isEmpty())
                        <p>No hay aulas registradas todavía.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Piso</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Tipo</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Capacidad</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($aulas as $aula)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $aula->nombre }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $aula->piso }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $aula->tipo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $aula->capacidad }}</td>

                                        {{-- Celda de Acciones (Editar y Eliminar) --}}
                                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">

                                            {{-- Enlace de Editar --}}
                                            <a href="{{ route('aulas.edit', $aula) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>

                                            {{-- Formulario de Eliminación --}}
                                            <form action="{{ route('aulas.destroy', $aula) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ml-4 text-red-600 hover:text-red-900">
                                                    Eliminar
                                                </button>
                                            </form>
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
