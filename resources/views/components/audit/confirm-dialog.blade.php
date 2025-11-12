@props(['message' => '¿Está seguro de realizar esta acción?', 'confirmText' => 'Confirmar', 'cancelText' => 'Cancelar'])

<div x-data="{ open: false }" {{ $attributes }}>
    <slot name="trigger" :open="open"></slot>

    <div x-show="open"
         x-cloak
         class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
         @click.away="open = false">
        <div class="bg-white rounded-lg shadow-2xl p-6 max-w-md w-full mx-4" @click.stop>
            <div class="flex items-center gap-4 mb-4">
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Confirmar Acción</h3>
            </div>

            <p class="text-gray-700 mb-6">{{ $message }}</p>

            <div class="flex gap-3 justify-end">
                <button @click="open = false"
                        type="button"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
                    {{ $cancelText }}
                </button>
                <slot name="confirm" :close="() => open = false"></slot>
            </div>
        </div>
    </div>
</div>
