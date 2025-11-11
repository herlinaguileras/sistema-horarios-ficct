<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Marcar Asistencia') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Mensaje de éxito --}}
                    @if(session('status'))
                        <div class="p-4 mb-6 text-green-800 bg-green-100 border border-green-200 rounded-lg">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h4 class="mb-6 text-lg font-medium">Marcar Asistencia para tus Clases</h4>

                    @if($horariosDocente->isEmpty())
                        <div class="p-6 text-center bg-gray-50 rounded-lg">
                            <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-4 text-gray-500">No tienes horarios asignados para marcar asistencia.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($horariosDocente as $horario)
                                <div class="p-5 transition-shadow duration-300 border rounded-lg shadow-sm hover:shadow-md">
                                    <h5 class="mb-3 text-base font-semibold text-gray-900">
                                        {{ $horario->grupo->materia->sigla }} - {{ $horario->grupo->materia->nombre }}
                                    </h5>
                                    <div class="mb-4 space-y-1 text-sm text-gray-600">
                                        <p class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <strong>Grupo:</strong> {{ $horario->grupo->nombre }}
                                        </p>
                                        <p class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            @php
                                                $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
                                            @endphp
                                            <strong>Día:</strong> {{ $dias[$horario->dia_semana] ?? 'Día ' . $horario->dia_semana }}
                                        </p>
                                        <p class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <strong>Horario:</strong> {{ date('H:i', strtotime($horario->hora_inicio)) }} - {{ date('H:i', strtotime($horario->hora_fin)) }}
                                        </p>
                                        <p class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <strong>Aula:</strong> {{ $horario->aula->nombre }}
                                        </p>
                                    </div>

                                    {{-- Mensajes de error específicos para este horario --}}
                                    @error('asistencia_error_' . $horario->id)
                                        <div class="p-3 mb-3 text-sm text-red-800 bg-red-100 border border-red-200 rounded-lg">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                    @error('password_error_' . $horario->id)
                                        <div class="p-3 mb-3 text-sm text-red-800 bg-red-100 border border-red-200 rounded-lg">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                    <div class="flex space-x-2">
                                        {{-- Botón QR - Abre nueva ventana con el código --}}
                                        <button type="button"
                                                onclick="window.open('{{ route('asistencias.generar.qr', $horario->id) }}', 'QR_Window', 'width=500,height=600,resizable=yes,scrollbars=yes')"
                                                class="flex-1 inline-flex justify-center items-center px-3 py-2 text-xs font-semibold text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM13 3a1 1 0 00-1 1v3a1 1 0 001 1h3a1 1 0 001-1V4a1 1 0 00-1-1h-3zm1 2v1h1V5h-1z" clip-rule="evenodd" />
                                                <path d="M11 4a1 1 0 10-2 0v1a1 1 0 002 0V4zM10 7a1 1 0 011 1v1h2a1 1 0 110 2h-3a1 1 0 01-1-1V8a1 1 0 011-1zM16 9a1 1 0 100 2 1 1 0 000-2zM9 13a1 1 0 011-1h1a1 1 0 110 2v2a1 1 0 11-2 0v-3zM7 11a1 1 0 100-2H4a1 1 0 100 2h3zM17 13a1 1 0 01-1 1h-2a1 1 0 110-2h2a1 1 0 011 1zM16 17a1 1 0 100-2h-3a1 1 0 100 2h3z" />
                                            </svg>
                                            Código QR
                                        </button>

                                        {{-- Botón Manual - Abre Modal --}}
                                        <button type="button"
                                                onclick="openModal('modal-{{ $horario->id }}')"
                                                class="flex-1 inline-flex justify-center items-center px-3 py-2 text-xs font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                            Manual
                                        </button>
                                    </div>
                                </div>

                                {{-- Modal para Registro Manual --}}
                                <div id="modal-{{ $horario->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                        {{-- Fondo del modal --}}
                                        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeModal('modal-{{ $horario->id }}')"></div>

                                        {{-- Centrado vertical --}}
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                        {{-- Contenido del modal --}}
                                        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                            <form action="{{ route('asistencias.marcar', $horario->id) }}" method="POST">
                                                @csrf
                                                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                                                    <div class="sm:flex sm:items-start">
                                                        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-blue-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        </div>
                                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                                                Marcar Asistencia Manual
                                                            </h3>
                                                            <div class="mt-4">
                                                                <p class="text-sm text-gray-700 mb-2">
                                                                    <strong>Materia:</strong> {{ $horario->grupo->materia->sigla }} - {{ $horario->grupo->materia->nombre }}
                                                                </p>
                                                                <p class="text-sm text-gray-700 mb-4">
                                                                    <strong>Grupo:</strong> {{ $horario->grupo->nombre }} |
                                                                    <strong>Aula:</strong> {{ $horario->aula->nombre }}
                                                                </p>

                                                                <div>
                                                                    <label for="password-{{ $horario->id }}" class="block mb-2 text-sm font-medium text-gray-700">
                                                                        Ingrese su contraseña para confirmar
                                                                    </label>
                                                                    <input type="password"
                                                                           id="password-{{ $horario->id }}"
                                                                           name="password"
                                                                           required
                                                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                                                           placeholder="Contraseña">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                                                    <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                        Confirmar Asistencia
                                                    </button>
                                                    <button type="button"
                                                            onclick="closeModal('modal-{{ $horario->id }}')"
                                                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                        Cancelar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Script para manejar modales --}}
                    <script>
                        function openModal(modalId) {
                            document.getElementById(modalId).classList.remove('hidden');
                            document.body.style.overflow = 'hidden';
                        }

                        function closeModal(modalId) {
                            document.getElementById(modalId).classList.add('hidden');
                            document.body.style.overflow = 'auto';
                            // Limpiar el campo de contraseña
                            const passwordInput = document.getElementById(modalId).querySelector('input[type="password"]');
                            if (passwordInput) {
                                passwordInput.value = '';
                            }
                        }

                        // Cerrar modal con tecla ESC
                        document.addEventListener('keydown', function(event) {
                            if (event.key === 'Escape') {
                                document.querySelectorAll('[id^="modal-"]').forEach(modal => {
                                    if (!modal.classList.contains('hidden')) {
                                        closeModal(modal.id);
                                    }
                                });
                            }
                        });
                    </script>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
