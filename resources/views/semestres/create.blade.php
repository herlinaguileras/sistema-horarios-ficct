<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Crear Nuevo Semestre') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('semestres.store') }}">
                        @csrf

                        {{-- Nombre --}}
                        <div class="mb-6">
                            <label for="nombre" class="block text-sm font-medium text-gray-700">
                                Nombre del Semestre *
                            </label>
                            <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                                   placeholder="Ej: Semestre I-2025, Gestión 2-2025"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">
                                <svg class="inline w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                Ejemplos: "Semestre I-2025", "Semestre II-2025", "Gestión 1-2025"
                            </p>
                            @error('nombre')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            {{-- Fecha de Inicio --}}
                            <div>
                                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">
                                    Fecha de Inicio *
                                </label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio') }}" required
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('fecha_inicio')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Fecha de Fin --}}
                            <div>
                                <label for="fecha_fin" class="block text-sm font-medium text-gray-700">
                                    Fecha de Fin *
                                </label>
                                <input type="date" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin') }}" required
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('fecha_fin')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Estado --}}
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Estado del Semestre *
                            </label>
                            <div class="space-y-3">
                                @foreach ($estados as $estadoOption)
                                    <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition
                                                  {{ old('estado') === $estadoOption ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                                        <input type="radio" name="estado" value="{{ $estadoOption }}" 
                                               {{ old('estado', 'Planificación') === $estadoOption ? 'checked' : '' }}
                                               class="mt-1 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                        <div class="ml-3">
                                            <span class="text-sm font-medium text-gray-900">{{ $estadoOption }}</span>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                @if($estadoOption === 'Planificación')
                                                    Semestre en preparación, aún no ha comenzado
                                                @elseif($estadoOption === 'Activo')
                                                    Semestre en curso (solo uno puede estar activo)
                                                @else
                                                    Semestre finalizado
                                                @endif
                                            </p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('estado')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-r-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        <strong>⚠️ Importante:</strong> Si seleccionas el estado "Activo", todos los demás semestres activos se cambiarán automáticamente a "Terminado".
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-end gap-4 mt-8">
                            <a href="{{ route('semestres.index') }}"
                               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Crear Semestre
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
