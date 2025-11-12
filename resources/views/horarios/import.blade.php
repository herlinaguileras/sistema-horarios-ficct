<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Importar Horarios') }}
            </h2>
            <a href="{{ route('horarios.plantilla') }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors duration-150 bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Descargar Plantilla
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Header con gradiente -->
            <div class="mb-6 overflow-hidden rounded-lg shadow-lg bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500">
                <div class="px-6 py-8 sm:px-8">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-16 h-16 mr-6 bg-white rounded-full bg-opacity-20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-white">Importar Horarios</h3>
                            <p class="mt-1 text-indigo-100">Carga masiva de horarios desde archivo Excel</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mensajes de Error -->
            @if($errors->any())
            <div class="mb-6 overflow-hidden bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-md">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-red-800">Error</h3>
                            <div class="mt-1 text-sm text-red-700">
                                {{ $errors->first() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Instrucciones del Formato -->
            <div class="mb-6 overflow-hidden bg-white rounded-lg shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Formato del Archivo Excel</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Columnas Requeridas -->
                        <div>
                            <h4 class="mb-4 text-sm font-bold text-blue-700 uppercase">📋 Columnas Requeridas</h4>
                            <ol class="space-y-3">
                                <li class="flex items-start p-3 transition-colors bg-white border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-sm">
                                    <span class="inline-flex items-center justify-center flex-shrink-0 w-6 h-6 mr-3 text-xs font-bold text-white bg-blue-500 rounded-full">1</span>
                                    <div>
                                        <div class="font-semibold text-gray-900">SIGLA</div>
                                        <div class="text-sm text-gray-600">Código de la materia (ej: MAT101)</div>
                                    </div>
                                </li>
                                <li class="flex items-start p-3 transition-colors bg-white border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-sm">
                                    <span class="inline-flex items-center justify-center flex-shrink-0 w-6 h-6 mr-3 text-xs font-bold text-white bg-blue-500 rounded-full">2</span>
                                    <div>
                                        <div class="font-semibold text-gray-900">SEMESTRE</div>
                                        <div class="text-sm text-gray-600">Número del semestre (ej: 1)</div>
                                    </div>
                                </li>
                                <li class="flex items-start p-3 transition-colors bg-white border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-sm">
                                    <span class="inline-flex items-center justify-center flex-shrink-0 w-6 h-6 mr-3 text-xs font-bold text-white bg-blue-500 rounded-full">3</span>
                                    <div>
                                        <div class="font-semibold text-gray-900">GRUPO</div>
                                        <div class="text-sm text-gray-600">Nombre del grupo (ej: F1, A, B)</div>
                                    </div>
                                </li>
                                <li class="flex items-start p-3 transition-colors bg-white border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-sm">
                                    <span class="inline-flex items-center justify-center flex-shrink-0 w-6 h-6 mr-3 text-xs font-bold text-white bg-blue-500 rounded-full">4</span>
                                    <div>
                                        <div class="font-semibold text-gray-900">MATERIA</div>
                                        <div class="text-sm text-gray-600">Nombre completo de la materia</div>
                                    </div>
                                </li>
                                <li class="flex items-start p-3 transition-colors bg-white border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-sm">
                                    <span class="inline-flex items-center justify-center flex-shrink-0 w-6 h-6 mr-3 text-xs font-bold text-white bg-blue-500 rounded-full">5</span>
                                    <div>
                                        <div class="font-semibold text-gray-900">DOCENTE</div>
                                        <div class="text-sm text-gray-600">Nombre completo del docente</div>
                                    </div>
                                </li>
                            </ol>
                        </div>

                        <!-- Horarios Repetibles -->
                        <div>
                            <h4 class="mb-4 text-sm font-bold text-green-700 uppercase">🔄 Horarios (Repetir)</h4>
                            <div class="p-4 mb-4 border-l-4 border-green-500 rounded-r-lg bg-green-50">
                                <p class="mb-3 text-sm font-medium text-green-900">Después de las 5 columnas base, repite estas 3 columnas por cada horario:</p>
                                <ul class="space-y-2 text-sm text-green-800">
                                    <li class="flex items-start">
                                        <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span><strong>DIA:</strong> Lun, Mar, Mie, Jue, Vie, Sáb, Dom</span>
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span><strong>HORA:</strong> HH:MM-HH:MM (ej: 18:15-20:30)</span>
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span><strong>AULA:</strong> Número o nombre del aula</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="p-4 border-l-4 border-blue-500 rounded-r-lg bg-blue-50">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-blue-900">Ejemplo Completo:</div>
                                        <code class="block p-2 mt-2 overflow-x-auto text-xs text-blue-900 bg-white rounded">MAT101 | 1 | F1 | CALCULO I | PEREZ | Mar | 18:15-20:30 | 14 | Jue | 18:15-20:30 | 14</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de carga -->
            <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                    <h3 class="flex items-center text-lg font-semibold text-gray-900">
                        <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Cargar Archivo
                    </h3>
                </div>
                                <div class="p-6">
                    <form action="{{ route('horarios.import.process') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <label for="archivo" class="block mb-2 text-sm font-medium text-gray-900">
                                Seleccionar archivo Excel (.xlsx, .xls, .csv)
                            </label>
                            <input type="file"
                                   class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 file:mr-4 file:py-3 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('archivo') border-red-500 @enderror"
                                   id="archivo"
                                   name="archivo"
                                   accept=".xlsx,.xls,.csv"
                                   required>
                            @error('archivo')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">
                                <svg class="inline w-4 h-4 mr-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                Tamaño máximo: 10 MB
                            </p>
                        </div>

                        <div class="flex flex-col gap-3 pt-4 border-t border-gray-200 sm:flex-row sm:justify-end">
                            <a href="{{ route('horarios.index') }}"
                               class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-gray-700 transition-all bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-all bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Importar Horarios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Advertencias Importantes -->
            <div class="mt-6 overflow-hidden border-l-4 border-yellow-500 rounded-r-lg shadow-md bg-gradient-to-r from-yellow-50 to-orange-50">
                <div class="p-6">
                    <div class="flex items-start mb-4">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="mb-3 text-lg font-bold text-yellow-900">⚠️ Advertencias Importantes</h4>
                            <ul class="space-y-3">
                                <li class="flex items-start p-3 bg-white border border-yellow-200 rounded-lg">
                                    <svg class="flex-shrink-0 w-5 h-5 mt-0.5 mr-3 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="flex-1">
                                        <strong class="text-yellow-900">Esta importación reemplazará TODOS los horarios existentes</strong>
                                        <p class="mt-1 text-sm text-yellow-800">Se eliminarán todos los registros actuales de horarios antes de importar los nuevos datos</p>
                                    </div>
                                </li>
                                <li class="flex items-start p-3 bg-white border border-yellow-200 rounded-lg">
                                    <svg class="flex-shrink-0 w-5 h-5 mt-0.5 mr-3 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="flex-1">
                                        <strong class="text-yellow-900">Asegúrate de hacer una copia de seguridad primero</strong>
                                        <p class="mt-1 text-sm text-yellow-800">Recomendamos exportar los horarios actuales antes de proceder con la importación</p>
                                    </div>
                                </li>
                                <li class="flex items-start p-3 bg-white border border-yellow-200 rounded-lg">
                                    <svg class="flex-shrink-0 w-5 h-5 mt-0.5 mr-3 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="flex-1">
                                        <strong class="text-yellow-900">Verifica el formato del archivo cuidadosamente</strong>
                                        <p class="mt-1 text-sm text-yellow-800">Un error en el formato puede causar fallos en la importación o datos incorrectos</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Características -->
            <div class="mt-6 overflow-hidden bg-white rounded-lg shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-amber-50 to-yellow-50">
                    <h3 class="flex items-center text-lg font-semibold text-gray-900">
                        <svg class="w-6 h-6 mr-3 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        Funcionalidades
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <!-- Auto-Creación -->
                        <div class="flex items-start p-4 transition-all bg-white border border-gray-200 rounded-lg hover:border-indigo-300 hover:shadow-md">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-12 h-12 bg-indigo-100 rounded-lg">
                                    <svg class="w-7 h-7 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-base font-semibold text-gray-900">Auto-Creación</h4>
                                <p class="mt-1 text-sm text-gray-600">Crea automáticamente docentes, materias y aulas que no existan</p>
                            </div>
                        </div>

                        <!-- Actualización -->
                        <div class="flex items-start p-4 transition-all bg-white border border-gray-200 rounded-lg hover:border-green-300 hover:shadow-md">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-base font-semibold text-gray-900">Actualización</h4>
                                <p class="mt-1 text-sm text-gray-600">Actualiza datos existentes automáticamente</p>
                            </div>
                        </div>

                        <!-- Validación -->
                        <div class="flex items-start p-4 transition-all bg-white border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-md">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                                    <svg class="w-7 h-7 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-base font-semibold text-gray-900">Validación</h4>
                                <p class="mt-1 text-sm text-gray-600">Valida formato de datos antes de importar</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
