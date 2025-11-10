<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Panel de Control Administrativo') }}
        </h2>
    </x-slot>

    {{-- Alpine.js component: uses $activeTab passed from controller --}}
    <div class="py-12" x-data="{ activeTab: '{{ $activeTab ?? 'horarios' }}' }">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="mb-4 text-lg font-semibold">Panel Administrativo</h3>

                    {{-- Pestañas de Navegación --}}
                    <div class="mb-6 border-b border-gray-200">
                        <nav class="flex -mb-px space-x-8" aria-label="Tabs">
                            {{-- Tab Horarios --}}
                            <button
                                @click="activeTab = 'horarios'"
                                :class="{
                                    'border-indigo-500 text-indigo-600': activeTab === 'horarios',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'horarios'
                                }"
                                class="px-1 py-4 text-sm font-medium border-b-2 whitespace-nowrap"
                            >
                                📅 Horario Semanal
                            </button>

                            {{-- Tab Asistencias --}}
                            <button
                                @click="activeTab = 'asistencias'"
                                :class="{
                                    'border-indigo-500 text-indigo-600': activeTab === 'asistencias',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'asistencias'
                                }"
                                class="px-1 py-4 text-sm font-medium border-b-2 whitespace-nowrap"
                            >
                                ✅ Asistencia Docente/Grupo
                            </button>

                            {{-- Tab Aulas Disponibles --}}
                            <button
                                @click="activeTab = 'aulas'"
                                :class="{
                                    'border-indigo-500 text-indigo-600': activeTab === 'aulas',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'aulas'
                                }"
                                class="px-1 py-4 text-sm font-medium border-b-2 whitespace-nowrap"
                            >
                                🏫 Aulas Disponibles
                            </button>
                        </nav>
                    </div>

                    {{-- Contenido Pestaña Horarios --}}
                    <div x-show="activeTab === 'horarios'">
                        @include('dashboards.partials.admin-horarios')
                    </div>

                    {{-- Contenido Pestaña Asistencias --}}
                    <div x-show="activeTab === 'asistencias'">
                        @include('dashboards.partials.admin-asistencias')
                    </div>

                    {{-- Contenido Pestaña Aulas Disponibles --}}
                    <div x-show="activeTab === 'aulas'">
                        @include('dashboards.partials.admin-aulas')
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
