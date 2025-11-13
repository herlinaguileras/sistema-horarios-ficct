/**
 * SCRIPT DE DIAGNÓSTICO COMPLETO
 * ===============================
 * Copia y pega este código completo en la consola del navegador
 * cuando tengas abierto: http://127.0.0.1:8000/dashboard?tab=horarios
 */

(function() {
    console.clear();
    console.log('%c═══════════════════════════════════════════════════════════', 'color: #3b82f6; font-weight: bold');
    console.log('%c   DIAGNÓSTICO DASHBOARD - BOTONES EXPORTACIÓN', 'color: #3b82f6; font-weight: bold; font-size: 16px');
    console.log('%c═══════════════════════════════════════════════════════════', 'color: #3b82f6; font-weight: bold');
    console.log('');

    let erroresEncontrados = 0;
    let advertenciasEncontradas = 0;

    // Test 1: Funciones JavaScript
    console.log('%c1️⃣ FUNCIONES JAVASCRIPT', 'color: #8b5cf6; font-weight: bold');
    console.log('───────────────────────────────────────────────────────────');

    if (typeof submitExportForm === 'function') {
        console.log('%c✅ submitExportForm existe', 'color: #10b981');
    } else {
        console.log('%c❌ submitExportForm NO existe', 'color: #ef4444');
        console.log('   PROBLEMA: La función no está cargada');
        console.log('   SOLUCIÓN: Ejecuta en PowerShell:');
        console.log('   php artisan view:clear && php artisan config:clear');
        erroresEncontrados++;
    }

    if (typeof exportPdfWithFilters === 'function') {
        console.log('%c✅ exportPdfWithFilters existe', 'color: #10b981');
    } else {
        console.log('%c❌ exportPdfWithFilters NO existe', 'color: #ef4444');
        console.log('   PROBLEMA: La función no está cargada');
        erroresEncontrados++;
    }

    if (typeof Alpine !== 'undefined') {
        console.log('%c✅ Alpine.js cargado', 'color: #10b981');
    } else {
        console.log('%c⚠️ Alpine.js no detectado', 'color: #f59e0b');
        advertenciasEncontradas++;
    }
    console.log('');

    // Test 2: Elementos del DOM
    console.log('%c2️⃣ ELEMENTOS DEL DOM', 'color: #8b5cf6; font-weight: bold');
    console.log('───────────────────────────────────────────────────────────');

    const form = document.getElementById('dashboardHorarioExportForm');
    if (form) {
        console.log('%c✅ Formulario Excel existe', 'color: #10b981');
        console.log('   ID:', form.id);
        console.log('   Action:', form.action);
        console.log('   Method:', form.method);
        console.log('   Inputs:', form.querySelectorAll('input').length);
    } else {
        console.log('%c❌ Formulario Excel NO existe', 'color: #ef4444');
        console.log('   ID buscado: dashboardHorarioExportForm');
        console.log('   PROBLEMA: El formulario no está en el DOM');
        console.log('   POSIBLE CAUSA: Estás en otra pestaña (no "Horario Semanal")');
        erroresEncontrados++;
    }

    const filters = document.getElementById('dashboardHorarioPdfFilters');
    if (filters) {
        console.log('%c✅ Contenedor de filtros existe', 'color: #10b981');
        console.log('   ID:', filters.id);
        console.log('   Dataset:', filters.dataset);
        const numFiltros = Object.keys(filters.dataset).length;
        console.log('   Filtros disponibles:', numFiltros);

        if (numFiltros === 0) {
            console.log('%c⚠️ No hay filtros aplicados', 'color: #f59e0b');
            advertenciasEncontradas++;
        }
    } else {
        console.log('%c❌ Contenedor de filtros NO existe', 'color: #ef4444');
        console.log('   ID buscado: dashboardHorarioPdfFilters');
        erroresEncontrados++;
    }
    console.log('');

    // Test 3: Botones
    console.log('%c3️⃣ BOTONES', 'color: #8b5cf6; font-weight: bold');
    console.log('───────────────────────────────────────────────────────────');

    const btnExcel = document.querySelector('button[onclick*="submitExportForm"]');
    if (btnExcel) {
        console.log('%c✅ Botón Excel encontrado', 'color: #10b981');
        console.log('   onclick:', btnExcel.getAttribute('onclick'));
        console.log('   disabled:', btnExcel.disabled);
        console.log('   visible:', btnExcel.offsetParent !== null);
    } else {
        console.log('%c❌ Botón Excel NO encontrado', 'color: #ef4444');
        erroresEncontrados++;
    }

    const btnPdf = document.querySelector('button[onclick*="exportPdfWithFilters"]');
    if (btnPdf) {
        console.log('%c✅ Botón PDF encontrado', 'color: #10b981');
        console.log('   onclick:', btnPdf.getAttribute('onclick'));
        console.log('   disabled:', btnPdf.disabled);
        console.log('   visible:', btnPdf.offsetParent !== null);
    } else {
        console.log('%c❌ Botón PDF NO encontrado', 'color: #ef4444');
        erroresEncontrados++;
    }
    console.log('');

    // Test 4: Pestaña activa (Alpine.js)
    console.log('%c4️⃣ PESTAÑA ACTIVA (ALPINE.JS)', 'color: #8b5cf6; font-weight: bold');
    console.log('───────────────────────────────────────────────────────────');

    const tabContainer = document.querySelector('[x-show="activeTab === \'horarios\'"]');
    if (tabContainer) {
        const isVisible = tabContainer.style.display !== 'none';
        if (isVisible) {
            console.log('%c✅ Pestaña "Horarios" está ACTIVA', 'color: #10b981');
        } else {
            console.log('%c❌ Pestaña "Horarios" está OCULTA', 'color: #ef4444');
            console.log('   SOLUCIÓN: Haz click en la pestaña "📅 Horario Semanal"');
            erroresEncontrados++;
        }
        console.log('   Display:', tabContainer.style.display || 'auto');
    } else {
        console.log('%c⚠️ Sistema de pestañas no detectado', 'color: #f59e0b');
        advertenciasEncontradas++;
    }
    console.log('');

    // Test 5: Prueba de exportación
    console.log('%c5️⃣ PRUEBA DE EXPORTACIÓN', 'color: #8b5cf6; font-weight: bold');
    console.log('───────────────────────────────────────────────────────────');

    if (form && btnExcel && typeof submitExportForm === 'function') {
        console.log('%c✅ Todo listo para exportar Excel', 'color: #10b981');
        console.log('   Ejecuta esto para probar:');
        console.log('%c   submitExportForm("dashboardHorarioExportForm", document.querySelector("button[onclick*=submitExportForm]"))',
                    'background: #1e293b; color: #60a5fa; padding: 5px; border-radius: 3px');
    } else {
        console.log('%c❌ NO se puede exportar Excel', 'color: #ef4444');
        console.log('   Elementos faltantes:');
        console.log('   - Formulario:', !!form);
        console.log('   - Botón:', !!btnExcel);
        console.log('   - Función:', typeof submitExportForm === 'function');
    }

    if (filters && btnPdf && typeof exportPdfWithFilters === 'function') {
        console.log('%c✅ Todo listo para exportar PDF', 'color: #10b981');
        console.log('   Ejecuta esto para probar:');
        console.log('%c   exportPdfWithFilters("http://127.0.0.1:8000/dashboard/export/horario-semanal-pdf", "dashboardHorarioPdfFilters")',
                    'background: #1e293b; color: #60a5fa; padding: 5px; border-radius: 3px');
    } else {
        console.log('%c❌ NO se puede exportar PDF', 'color: #ef4444');
        console.log('   Elementos faltantes:');
        console.log('   - Filtros:', !!filters);
        console.log('   - Botón:', !!btnPdf);
        console.log('   - Función:', typeof exportPdfWithFilters === 'function');
    }
    console.log('');

    // Resumen final
    console.log('%c═══════════════════════════════════════════════════════════', 'color: #3b82f6; font-weight: bold');
    console.log('%c   RESUMEN', 'color: #3b82f6; font-weight: bold; font-size: 14px');
    console.log('%c═══════════════════════════════════════════════════════════', 'color: #3b82f6; font-weight: bold');

    if (erroresEncontrados === 0 && advertenciasEncontradas === 0) {
        console.log('%c✅ TODOS LOS TESTS PASARON', 'color: #10b981; font-size: 16px; font-weight: bold');
        console.log('');
        console.log('%c🎉 Los botones deberían funcionar perfectamente', 'color: #10b981');
        console.log('');
        console.log('Prueba hacer click en:');
        console.log('1. Botón "📊 Excel" - Debe descargar archivo .xlsx');
        console.log('2. Botón "📄 PDF" - Debe abrir nueva ventana y descargar .pdf');
        console.log('');
        console.log('Si NO funcionan, ejecuta en consola:');
        console.log('%c   submitExportForm("dashboardHorarioExportForm", document.querySelector("button[onclick*=submitExportForm]"))',
                    'background: #1e293b; color: #60a5fa; padding: 5px; border-radius: 3px');
    } else {
        console.log('%c❌ SE ENCONTRARON PROBLEMAS', 'color: #ef4444; font-size: 16px; font-weight: bold');
        console.log('');
        console.log('Errores encontrados:', erroresEncontrados);
        console.log('Advertencias:', advertenciasEncontradas);
        console.log('');
        console.log('%c📋 ACCIONES RECOMENDADAS:', 'color: #f59e0b; font-weight: bold');

        if (typeof submitExportForm !== 'function' || typeof exportPdfWithFilters !== 'function') {
            console.log('');
            console.log('%c1. Limpiar cache de Laravel', 'color: #3b82f6; font-weight: bold');
            console.log('   Ejecuta en PowerShell:');
            console.log('   php artisan view:clear');
            console.log('   php artisan config:clear');
            console.log('   php artisan route:clear');
        }

        if (!form || !filters) {
            console.log('');
            console.log('%c2. Verificar pestaña activa', 'color: #3b82f6; font-weight: bold');
            console.log('   Asegúrate de estar en la pestaña "📅 Horario Semanal"');
            console.log('   Haz click en el tab si no está activo');
        }

        console.log('');
        console.log('%c3. Refrescar página', 'color: #3b82f6; font-weight: bold');
        console.log('   Presiona Ctrl+Shift+R (forzar recarga sin cache)');

        console.log('');
        console.log('%c4. Verificar Network', 'color: #3b82f6; font-weight: bold');
        console.log('   Ve a la pestaña Network en DevTools');
        console.log('   Verifica que se carguen:');
        console.log('   - /build/assets/app-*.js');
        console.log('   - /build/assets/app-*.css');
    }

    console.log('');
    console.log('%c═══════════════════════════════════════════════════════════', 'color: #3b82f6; font-weight: bold');
    console.log('');
    console.log('💡 Para más ayuda, abre:');
    console.log('   http://127.0.0.1:8000/diagnostico-dashboard.html');
    console.log('');

    // Guardar resultados en objeto global para referencia
    window.diagnostico = {
        timestamp: new Date().toISOString(),
        funciones: {
            submitExportForm: typeof submitExportForm === 'function',
            exportPdfWithFilters: typeof exportPdfWithFilters === 'function',
            Alpine: typeof Alpine !== 'undefined'
        },
        elementos: {
            formulario: !!form,
            filtros: !!filters,
            botonExcel: !!btnExcel,
            botonPdf: !!btnPdf
        },
        resumen: {
            errores: erroresEncontrados,
            advertencias: advertenciasEncontradas,
            todoOk: erroresEncontrados === 0
        }
    };

    console.log('%cResultados guardados en: window.diagnostico', 'color: #64748b; font-style: italic');

})();
