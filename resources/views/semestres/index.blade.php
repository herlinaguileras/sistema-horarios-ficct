<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Gestión de Semestres') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Mensaje de éxito --}}
            @if (session('status'))
                <div class="mb-4 overflow-hidden bg-green-100 border-l-4 border-green-500 rounded shadow-sm">
                    <div class="flex items-center px-4 py-3">
                        <svg class="w-6 h-6 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-green-700">{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            {{-- Mensaje de error --}}
            @if ($errors->any())
                <div class="mb-4 overflow-hidden bg-red-100 border-l-4 border-red-500 rounded shadow-sm">
                    <div class="px-4 py-3">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center text-red-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $error }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Semestres Académicos</h3>
                            <p class="mt-1 text-sm text-gray-600">Gestiona los períodos académicos del sistema</p>
                        </div>
                        <a href="{{ route('semestres.create') }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Nuevo Semestre
                        </a>
                    </div>

                    @if($semestres->isEmpty())
                        <div class="p-8 text-center border-2 border-gray-200 border-dashed rounded-lg bg-gray-50">
                            <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No hay semestres registrados</h3>
                            <p class="mt-2 text-sm text-gray-600">Comienza creando tu primer semestre académico.</p>
                            <div class="p-4 mt-4 text-sm text-left bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="font-semibold text-blue-900">⚠️ Importante:</p>
                                <p class="mt-1 text-blue-800">Necesitas crear un semestre con estado <strong>"Activo"</strong> para poder ver los reportes de horarios y asistencias en el Dashboard.</p>
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Semestre
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Período
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                            Estado
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                            Grupos
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Acciones</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($semestres as $semestre)
                                        <tr class="transition hover:bg-gray-50 {{ $semestre->isActivo() ? 'bg-green-50' : '' }}">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $semestre->nombre }}</div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $semestre->grupos()->count() }} grupo(s) asignado(s)
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $semestre->fecha_inicio->format('d/m/Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    hasta {{ $semestre->fecha_fin->format('d/m/Y') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                                @if($semestre->estado === 'Activo')
                                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                                        <span class="w-2 h-2 mr-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                                        Activo
                                                    </span>
                                                @elseif($semestre->estado === 'Planificación')
                                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                                                        <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                        </svg>
                                                        Planificación
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold text-gray-700 bg-gray-200 rounded-full">
                                                        <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                        </svg>
                                                        Terminado
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                                                {{ $semestre->grupos()->count() }}
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-3">
                                                    @if(!$semestre->isActivo())
                                                        <form method="POST" action="{{ route('semestres.toggle-activo', $semestre) }}" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="text-green-600 hover:text-green-900 transition">
                                                                Activar
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('semestres.edit', $semestre) }}" 
                                                       class="text-indigo-600 hover:text-indigo-900 transition">
                                                        Editar
                                                    </a>
                                                    @if(!$semestre->isActivo() && $semestre->grupos()->count() === 0)
                                                        <form method="POST" action="{{ route('semestres.destroy', $semestre) }}" 
                                                              onsubmit="return confirm('¿Estás seguro de eliminar este semestre?');" 
                                                              class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900 transition">
                                                                Eliminar
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-800">
                                        <strong>ℹ️ Estados del semestre:</strong>
                                    </p>
                                    <ul class="mt-2 text-sm text-blue-700 list-disc list-inside">
                                        <li><strong>Planificación:</strong> Semestre en preparación, aún no ha comenzado</li>
                                        <li><strong>Activo:</strong> Semestre en curso (solo uno puede estar activo)</li>
                                        <li><strong>Terminado:</strong> Semestre finalizado</li>
                                    </ul>
                                    <p class="mt-2 text-sm text-blue-800">
                                        El semestre activo es el que se muestra en el Dashboard y en los reportes.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
