<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Código QR de Asistencia') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes de éxito/error/info --}}
            @if(session('success'))
                <div class="p-4 mb-6 text-green-700 bg-green-100 border-l-4 border-green-500 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="font-bold">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-6 text-red-700 bg-red-100 border-l-4 border-red-500 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="font-bold">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if(session('info'))
                <div class="p-4 mb-6 text-blue-700 bg-blue-100 border-l-4 border-blue-500 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="font-bold">{{ session('info') }}</p>
                    </div>
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Mis Clases de Hoy --}}
                    <div class="p-6 mb-6 border border-blue-200 rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50">
                        <h3 class="flex items-center gap-2 mb-4 text-xl font-bold text-indigo-900">
                            📅 Clases de Hoy - {{ \Carbon\Carbon::now()->format('d/m/Y') }}
                        </h3>

                        @php
                            $hoy = \Carbon\Carbon::now();
                            $diaSemana = $hoy->dayOfWeekIso; // 1=Lunes, 2=Martes, ..., 7=Domingo (ISO 8601)

                            // Obtener horarios del docente para hoy
                            // El docente está relacionado a través de grupo_id
                            $horariosHoy = \App\Models\Horario::whereHas('grupo', function($query) use ($docente) {
                                    $query->where('docente_id', $docente->id);
                                })
                                ->where('dia_semana', $diaSemana)
                                ->with(['grupo.materia', 'aula'])
                                ->orderBy('hora_inicio')
                                ->get();

                            $ahora = $hoy->format('H:i:s');
                        @endphp

                        @if($horariosHoy->isEmpty())
                            <div class="py-8 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="mt-4 font-semibold text-gray-600">No tienes clases programadas para hoy</p>
                                <p class="mt-2 text-sm text-gray-500">Revisa tu horario semanal en el dashboard</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($horariosHoy as $horario)
                                    @php
                                        // Calcular si es la clase actual (15 min antes del inicio hasta 15 min después del inicio)
                                        $horaInicio = \Carbon\Carbon::parse($hoy->toDateString() . ' ' . $horario->hora_inicio);
                                        $horaFin = \Carbon\Carbon::parse($hoy->toDateString() . ' ' . $horario->hora_fin);
                                        $ventanaInicio = $horaInicio->copy()->subMinutes(15);
                                        $ventanaFin = $horaInicio->copy()->addMinutes(15);

                                        // Usar betweenIncluded para incluir los límites
                                        $esClaseActual = $hoy->betweenIncluded($ventanaInicio, $ventanaFin);
                                        $yaTermino = $ahora > $horario->hora_fin;

                                        // Verificar si ya marcó asistencia hoy
                                        $asistenciaHoy = \App\Models\Asistencia::where('horario_id', $horario->id)
                                            ->where('docente_id', $docente->id)
                                            ->whereDate('fecha', $hoy->toDateString())
                                            ->first();
                                    @endphp

                                    <div class="bg-white p-4 rounded-lg shadow-sm border-2 {{ $esClaseActual ? 'border-green-500 ring-2 ring-green-200' : 'border-gray-200' }}">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-2">
                                                    @if($esClaseActual)
                                                        <span class="px-3 py-1 text-xs font-bold text-white bg-green-500 rounded-full animate-pulse">
                                                            🔴 AHORA
                                                        </span>
                                                    @elseif($yaTermino)
                                                        <span class="px-3 py-1 text-xs font-semibold text-white bg-gray-400 rounded-full">
                                                            ⏱️ Terminó
                                                        </span>
                                                    @else
                                                        <span class="px-3 py-1 text-xs font-semibold text-white bg-blue-500 rounded-full">
                                                            ⏰ Próxima
                                                        </span>
                                                    @endif

                                                    @if($asistenciaHoy)
                                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                                            ✅ Registrada {{ \Carbon\Carbon::parse($asistenciaHoy->hora_registro)->format('H:i') }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <h4 class="text-lg font-bold text-gray-900">{{ $horario->grupo->materia->nombre }}</h4>
                                                <p class="text-sm text-gray-600">{{ $horario->grupo->materia->sigla }} - Grupo {{ $horario->grupo->nombre }}</p>
                                                <div class="flex items-center gap-4 mt-2 text-sm text-gray-700">
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                        </svg>
                                                        {{ $horario->aula->nombre }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="ml-4">
                                                @if($asistenciaHoy)
                                                    <button disabled class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-300 rounded-lg cursor-not-allowed">
                                                        Ya Registrada
                                                    </button>
                                                @elseif($yaTermino)
                                                    <button disabled class="px-4 py-2 text-sm text-gray-500 bg-gray-200 rounded-lg cursor-not-allowed">
                                                        Clase Terminada
                                                    </button>
                                                @else
                                                    <form method="POST" action="{{ route('asistencia.qr.marcar', $horario) }}" class="inline">
                                                        @csrf
                                                        <button
                                                            type="submit"
                                                            onclick="return confirm('¿Confirmar asistencia para {{ $horario->grupo->materia->sigla }}?')"
                                                            class="{{ $esClaseActual ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white px-4 py-2 rounded-lg font-semibold text-sm transition duration-150 shadow-md hover:shadow-lg">
                                                            {{ $esClaseActual ? '✓ Marcar Asistencia' : 'Marcar Asistencia' }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="p-3 mt-4 border border-blue-200 rounded-lg bg-blue-50">
                                <p class="text-sm text-blue-800">
                                    <strong>💡 Tip:</strong> Puedes marcar asistencia desde 15 minutos antes hasta 15 minutos después del inicio de la clase (ventana de 30 minutos).
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        {{-- Información del Docente --}}
                        <div>
                            <h3 class="mb-4 text-lg font-semibold text-gray-800">📋 Información del Docente</h3>

                            <div class="p-4 space-y-3 rounded-lg bg-gray-50">
                                <div>
                                    <span class="text-sm font-medium text-gray-600">Nombre:</span>
                                    <p class="font-semibold text-gray-900">{{ $docente->user->name }}</p>
                                </div>

                                <div>
                                    <span class="text-sm font-medium text-gray-600">Código Docente:</span>
                                    <p class="font-mono font-bold text-gray-900">{{ $docente->codigo_docente }}</p>
                                </div>

                                <div>
                                    <span class="text-sm font-medium text-gray-600">CI:</span>
                                    <p class="text-gray-900">{{ $docente->carnet_identidad }}</p>
                                </div>

                                @if($docente->titulo)
                                <div>
                                    <span class="text-sm font-medium text-gray-600">Título:</span>
                                    <p class="text-gray-900">{{ $docente->titulo }}</p>
                                </div>
                                @endif
                            </div>

                            <div class="p-4 mt-6 border-l-4 border-blue-500 rounded bg-blue-50">
                                <h4 class="mb-2 font-semibold text-blue-900">ℹ️ Instrucciones:</h4>
                                <ul class="space-y-1 text-sm text-blue-800 list-disc list-inside">
                                    <li>Presenta este QR al ingresar a tu clase</li>
                                    <li>El código se actualiza automáticamente cada 5 minutos</li>
                                    <li>Es personal e intransferible</li>
                                    <li>Solo válido durante tus horarios de clase</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Código QR --}}
                        <div class="flex flex-col items-center justify-center">
                            <div class="p-6 bg-white border-4 border-gray-200 rounded-lg shadow-lg">
                                <div id="qr-container" class="mb-4">
                                    {!! $qrCode !!}
                                </div>

                                <div class="text-center">
                                    <p class="mb-2 text-sm text-gray-600">
                                        🔒 QR Seguro y Firmado
                                    </p>
                                    <p class="p-2 font-mono text-xs text-gray-500 bg-gray-100 rounded">
                                        Token: {{ substr($docente->qr_token, 0, 16) }}...
                                    </p>
                                </div>
                            </div>

                            {{-- Botón para recargar QR --}}
                            <button
                                onclick="reloadQr()"
                                class="flex items-center gap-2 px-6 py-2 mt-4 font-bold text-white transition duration-150 bg-blue-500 rounded-lg hover:bg-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                </svg>
                                Actualizar QR
                            </button>

                            <p class="mt-2 text-xs text-center text-gray-500">
                                Última actualización: <span id="last-update">{{ now()->format('H:i:s') }}</span>
                            </p>
                        </div>
                    </div>

                    {{-- Advertencia de seguridad --}}
                    <div class="p-4 mt-6 border-l-4 border-yellow-400 rounded bg-yellow-50">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-yellow-400 mt-0.5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <h4 class="text-sm font-semibold text-yellow-800">⚠️ Importante - Seguridad</h4>
                                <p class="mt-1 text-sm text-yellow-700">
                                    No compartas capturas de pantalla de este QR. Cada código es único, firmado digitalmente
                                    y vinculado a tu identidad. El uso indebido será detectado y registrado.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Botón volver --}}
                    <div class="flex justify-end mt-6">
                        <a href="{{ route('dashboard') }}"
                           class="px-6 py-2 font-bold text-gray-800 transition duration-150 bg-gray-300 rounded-lg hover:bg-gray-400">
                            ← Volver al Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script para auto-recargar QR --}}
    <script>
        // Auto-reload cada 5 minutos
        let autoReloadInterval = setInterval(reloadQr, 5 * 60 * 1000);

        function reloadQr() {
            // Recargar la página para obtener nuevo QR con timestamp actualizado
            location.reload();
        }

        // Actualizar timestamp cada segundo
        setInterval(() => {
            const now = new Date();
            document.getElementById('last-update').textContent =
                now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }, 1000);
    </script>
</x-app-layout>
