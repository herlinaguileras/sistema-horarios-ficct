<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Mi Horario Semanal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Información del Docente --}}
                    <div class="mb-6">
                        <h3 class="mb-2 text-lg font-semibold">Docente: {{ $docente->user->name }}</h3>
                        <p class="text-sm text-gray-600">Código: {{ $docente->codigo_docente }}</p>
                    </div>

                    {{-- Calendario Semanal --}}
                    @if($horariosDocente->isEmpty())
                        <div class="p-6 text-center bg-gray-50 rounded-lg">
                            <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="mt-4 text-gray-500">No tienes horarios asignados actualmente.</p>
                        </div>
                    @else
                        @include('dashboards.partials.docente-horario-calendario')
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
