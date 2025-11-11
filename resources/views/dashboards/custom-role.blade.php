<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Panel de Control - {{ $role->description ?? $role->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if(empty($modules))
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <h3 class="mb-2 text-xl font-semibold text-gray-700">Sin módulos asignados</h3>
                        <p class="text-gray-500">Tu rol actual no tiene módulos configurados. Contacta al administrador.</p>
                    </div>
                </div>
            @else
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800">Módulos Disponibles</h3>
                        <p class="mt-1 text-sm text-gray-600">Selecciona un módulo para acceder a sus funcionalidades</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($modules as $module)
                        <a href="{{ route($module['route']) }}" class="block p-6 overflow-hidden transition-all duration-200 bg-white border-2 border-gray-200 rounded-lg shadow hover:shadow-xl hover:-translate-y-1">
                            <div class="flex items-center mb-4">
                                <div class="flex items-center justify-center w-12 h-12 mr-4 text-white rounded-lg bg-{{ $module['color'] }}-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-800">{{ $module['name'] }}</h3>
                            </div>
                            <p class="text-sm text-gray-600">{{ $module['description'] }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
