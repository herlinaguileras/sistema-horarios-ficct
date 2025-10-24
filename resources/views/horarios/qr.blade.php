@php
    // Import the QrCode facade explicitly at the very top
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
    // Helper array to convert day number to name
    $nombresDias = [ 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
    // Assign the name to a variable for cleaner use in the header
    $diaNombre = $nombresDias[$horario->dia_semana] ?? 'Día inválido';
    $qrContent = route('asistencias.marcar.qr', $horario);
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{-- Title uses loaded relationships --}}
            Código QR para Asistencia: {{ $horario->grupo->materia->sigla }} - Grupo {{ $horario->grupo->nombre }}
        </h2>
        <p class="text-sm text-gray-600">
            {{-- Display details using the $diaNombre variable --}}
            {{ $diaNombre }} | {{ date('H:i', strtotime($horario->hora_inicio)) }} - {{ date('H:i', strtotime($horario->hora_fin)) }} | {{ $horario->aula->nombre }} |
            Docente: {{ $horario->grupo->docente->user->name }}
        </p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8"> {{-- Smaller width --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-center text-gray-900"> {{-- Centered content --}}

                    <h3 class="mb-4 text-lg font-medium">Escanee para marcar asistencia</h3>

                    @php
                        // Define the content for the QR code (the URL to hit)
                        // Using 'asistencias.marcar' route for now
                        $qrContent = route('asistencias.marcar', $horario);
                    @endphp

                    {{-- Container for the QR Code --}}
                    <div class="inline-block p-4 border rounded-lg">
                         {{-- Generate the QR code SVG using the imported facade --}}
                         {!! QrCode::size(250)->generate($qrContent) !!}
                    </div>

                    {{-- Display the QR content (optional, for debugging) --}}
                    <p class="mt-4 text-xs text-gray-500 break-all">Contenido: {{ $qrContent }}</p>

                    {{-- Back Button --}}
                    <div class="mt-6">
                         {{-- Link back to the main dashboard --}}
                         <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-900">
                             &larr; Volver al Dashboard
                         </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
