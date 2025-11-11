<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Panel de Control') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mb-2 text-lg font-semibold text-gray-900">
                            Bienvenido al Sistema de Horarios FICCT
                        </h3>
                        <p class="text-gray-600">
                            Tu cuenta no tiene permisos asignados actualmente.
                            Por favor, contacta al administrador del sistema para obtener acceso.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
