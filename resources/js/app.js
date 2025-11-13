import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/**
 * Función global para manejar el envío de formularios de exportación
 * con retroalimentación visual.
 *
 * @param {string} formId - ID del formulario a enviar
 * @param {HTMLElement} button - Botón que disparó la acción
 */
window.submitExportForm = function(formId, button) {
    const form = document.getElementById(formId);

    if (!form) {
        console.error('❌ Formulario no encontrado:', formId);
        alert('Error: No se pudo encontrar el formulario de exportación.');
        return;
    }

    // Deshabilitar botón
    button.disabled = true;

    // Cambiar texto del botón a estado "loading"
    const btnText = button.querySelector('.btn-text');
    const btnLoading = button.querySelector('.btn-loading');

    if (btnText) btnText.classList.add('hidden');
    if (btnLoading) btnLoading.classList.remove('hidden');

    console.log('📤 Enviando formulario de exportación:', formId);

    // Enviar el formulario
    form.submit();

    // Restaurar el botón después de 3 segundos
    // (La descarga ya habrá iniciado)
    setTimeout(() => {
        button.disabled = false;
        if (btnText) btnText.classList.remove('hidden');
        if (btnLoading) btnLoading.classList.add('hidden');
        console.log('✅ Exportación iniciada correctamente');
    }, 3000);
};

/**
 * Función para exportar PDF con filtros
 * Construye una URL con parámetros de filtros y abre en nueva ventana
 *
 * @param {string} baseUrl - URL base del endpoint de exportación PDF
 * @param {string} filtersContainerId - ID del contenedor con los filtros (data attributes)
 */
window.exportPdfWithFilters = function(baseUrl, filtersContainerId) {
    const filtersContainer = document.getElementById(filtersContainerId);

    if (!filtersContainer) {
        console.error('❌ Contenedor de filtros no encontrado:', filtersContainerId);
        window.open(baseUrl, '_blank');
        return;
    }

    // Construir parámetros de URL desde data attributes
    const params = new URLSearchParams();
    const dataset = filtersContainer.dataset;

    for (const [key, value] of Object.entries(dataset)) {
        if (value && value.trim() !== '') {
            params.append(key, value);
            console.log(`🔍 Filtro aplicado: ${key} = ${value}`);
        }
    }

    // Construir URL final
    const finalUrl = params.toString()
        ? `${baseUrl}?${params.toString()}`
        : baseUrl;

    console.log('📄 Abriendo PDF con filtros:', finalUrl);

    // Abrir en nueva ventana
    window.open(finalUrl, '_blank');
};
