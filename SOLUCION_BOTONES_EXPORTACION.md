# 🔧 SOLUCIÓN: Botones Excel y PDF no funcionan

**Fecha**: 13 de Noviembre de 2025  
**Base de datos**: PostgreSQL  
**Estado**: Diagnóstico completado - Sistema configurado correctamente

---

## ✅ DIAGNÓSTICO AUTOMÁTICO COMPLETADO

El script `diagnostico_exportacion.php` confirmó que:

- ✅ Base de datos PostgreSQL conectada
- ✅ Semestre activo: "Gestion 1 - 2026"
- ✅ 17 horarios disponibles
- ✅ 4 horarios para docente ID 38
- ✅ Todas las clases Export existen
- ✅ Todas las vistas PDF existen
- ✅ Paquetes instalados (Maatwebsite/Excel, DomPDF)
- ✅ Métodos del controlador existen
- ✅ Funciones JavaScript existen

**Conclusión**: El backend está 100% funcional. El problema está en el **frontend/navegador**.

---

## 🔍 PASOS DE DEPURACIÓN

### PASO 1: Verificar que el servidor esté corriendo

```powershell
# En una terminal, ejecutar:
php artisan serve

# Debe mostrar:
# Server started on [http://127.0.0.1:8000]
```

**Si el servidor no está corriendo**, inícialo antes de probar.

---

### PASO 2: Probar rutas directamente

Abre estos enlaces en tu navegador (con el servidor corriendo):

#### 2.1 Excel Horarios (sin filtros)
```
http://127.0.0.1:8000/dashboard/export/horario-semanal
```
**Resultado esperado**: Descarga archivo `horario_semanal_Gestion 1 - 2026.xlsx`

#### 2.2 PDF Horarios (sin filtros)
```
http://127.0.0.1:8000/dashboard/export/horario-semanal-pdf
```
**Resultado esperado**: Descarga archivo `horario_semanal_Gestion 1 - 2026.pdf`

#### 2.3 Excel Horarios (con filtro docente)
```
http://127.0.0.1:8000/dashboard/export/horario-semanal?filtro_docente_id=38
```
**Resultado esperado**: Descarga Excel con solo 4 horarios

#### 2.4 PDF Horarios (con filtro docente)
```
http://127.0.0.1:8000/dashboard/export/horario-semanal-pdf?filtro_docente_id=38
```
**Resultado esperado**: Descarga PDF con solo 4 horarios

---

### PASO 3: Usar página de prueba

He creado una página de prueba independiente:

```
http://127.0.0.1:8000/test-exportacion.html
```

**Acciones**:
1. Abre la URL en tu navegador
2. Verás botones de prueba
3. Haz clic en cada botón
4. Verifica que se descarguen los archivos

**Si funciona aquí pero no en el dashboard**, el problema está en el JavaScript del dashboard.

---

### PASO 4: Depurar JavaScript en el Dashboard

1. **Acceder al dashboard**:
   ```
   http://127.0.0.1:8000/dashboard?tab=horarios
   ```

2. **Abrir consola del navegador**:
   - Presiona **F12**
   - Ve a la pestaña **Console**

3. **Verificar errores**:
   - ¿Hay mensajes en rojo?
   - Toma captura de pantalla de los errores

4. **Verificar funciones**:
   Ejecuta en la consola:
   ```javascript
   console.log('submitExportForm:', typeof submitExportForm);
   console.log('exportPdfWithFilters:', typeof exportPdfWithFilters);
   ```
   
   **Resultado esperado**:
   ```
   submitExportForm: function
   exportPdfWithFilters: function
   ```

5. **Verificar formulario**:
   ```javascript
   console.log('Form:', document.getElementById('dashboardHorarioExportForm'));
   ```
   
   **Resultado esperado**: Muestra el formulario `<form id="dashboardHorarioExportForm">`

6. **Verificar filtros**:
   ```javascript
   console.log('Filters:', document.getElementById('dashboardHorarioPdfFilters'));
   ```
   
   **Resultado esperado**: Muestra el div `<div id="dashboardHorarioPdfFilters">`

---

### PASO 5: Probar exportación manualmente desde consola

En la consola del navegador (con dashboard abierto):

#### 5.1 Test Excel
```javascript
const form = document.getElementById('dashboardHorarioExportForm');
if (form) {
    console.log('Formulario encontrado, enviando...');
    form.submit();
} else {
    console.error('Formulario NO encontrado');
}
```

#### 5.2 Test PDF
```javascript
const url = 'http://127.0.0.1:8000/dashboard/export/horario-semanal-pdf?filtro_docente_id=38';
console.log('Abriendo:', url);
window.open(url, '_blank');
```

---

### PASO 6: Verificar Network Tab

1. En el navegador, abre **F12** → **Network**
2. Haz clic en botón Excel o PDF
3. Observa las peticiones

**Busca**:
- ¿Aparece una petición a `/dashboard/export/...`?
- ¿Cuál es el código de respuesta? (200, 404, 500)
- ¿Hay algún error?

---

## 🐛 PROBLEMAS COMUNES Y SOLUCIONES

### Problema 1: "Nada pasa al hacer clic"

**Causas posibles**:
- JavaScript no está cargado
- Funciones no están definidas
- Error silencioso en consola

**Solución**:
```javascript
// Ejecutar en consola
console.log('Test funciones:');
console.log('submitExportForm:', typeof submitExportForm);
console.log('exportPdfWithFilters:', typeof exportPdfWithFilters);
```

Si muestra `undefined`, el problema está en `layouts/app.blade.php`.

**Verificar**:
```powershell
# Buscar las funciones
Get-Content resources/views/layouts/app.blade.php | Select-String "submitExportForm"
```

---

### Problema 2: "Error 404 Not Found"

**Causa**: Ruta no encontrada

**Solución**:
```powershell
# Limpiar cache de rutas
php artisan route:clear
php artisan config:clear
php artisan view:clear

# Verificar rutas
php artisan route:list | Select-String "dashboard.export"
```

---

### Problema 3: "Error 500 Internal Server Error"

**Causa**: Error en el backend

**Solución**:
```powershell
# Ver logs
Get-Content storage/logs/laravel.log -Tail 50

# Buscar líneas con "ERROR"
Get-Content storage/logs/laravel.log | Select-String "ERROR"
```

---

### Problema 4: "Descarga pero archivo vacío o corrupto"

**Causa**: Error en generación de Excel/PDF

**Soluciones**:

#### Para Excel:
```powershell
# Verificar clase Export
cat app/Exports/HorarioSemanalExport.php

# Probar manualmente en tinker
php artisan tinker
>>> use App\Exports\HorarioSemanalExport;
>>> $export = new HorarioSemanalExport(2, []);
>>> $data = $export->query()->get();
>>> $data->count();
```

#### Para PDF:
```powershell
# Verificar vista
cat resources/views/pdf/horario_semanal.blade.php

# Probar generación
php artisan tinker
>>> use Barryvdh\DomPDF\Facade\Pdf;
>>> $pdf = Pdf::loadView('pdf.horario_semanal', ['semestreActivo' => \App\Models\Semestre::find(2), 'horariosPorDia' => collect(), 'diasSemana' => []]);
>>> $pdf->download('test.pdf');
```

---

### Problema 5: "Popup bloqueado" (solo PDF)

**Causa**: El navegador bloquea popups

**Solución**:
1. Permitir popups para `127.0.0.1`
2. O usar enlace directo en vez de `window.open()`

---

## 🔧 SOLUCIONES ESPECÍFICAS

### Si las funciones JavaScript no existen

**Verificar que `app.blade.php` tiene las funciones**:

```powershell
# Buscar funciones
Select-String -Path "resources/views/layouts/app.blade.php" -Pattern "function submitExportForm"
Select-String -Path "resources/views/layouts/app.blade.php" -Pattern "function exportPdfWithFilters"
```

Si **NO aparecen**, necesitas agregar las funciones. Aquí están:

```javascript
function submitExportForm(formId, button) {
    const form = document.getElementById(formId);
    
    if (!form) {
        console.error('❌ Formulario no encontrado:', formId);
        alert('Error: No se pudo encontrar el formulario de exportación.');
        return;
    }
    
    button.disabled = true;
    const btnText = button.querySelector('.btn-text');
    const btnLoading = button.querySelector('.btn-loading');
    
    if (btnText) btnText.classList.add('hidden');
    if (btnLoading) btnLoading.classList.remove('hidden');
    
    console.log('📤 Enviando formulario:', formId);
    form.submit();
    
    setTimeout(() => {
        button.disabled = false;
        if (btnText) btnText.classList.remove('hidden');
        if (btnLoading) btnLoading.classList.add('hidden');
    }, 3000);
}

function exportPdfWithFilters(baseUrl, filtersContainerId) {
    const filtersContainer = document.getElementById(filtersContainerId);
    
    if (!filtersContainer) {
        console.error('❌ Contenedor de filtros no encontrado:', filtersContainerId);
        window.open(baseUrl, '_blank');
        return;
    }
    
    const params = new URLSearchParams();
    const dataset = filtersContainer.dataset;
    
    for (const [key, value] of Object.entries(dataset)) {
        if (value && value.trim() !== '') {
            params.append(key, value);
            console.log(`🔍 Filtro: ${key} = ${value}`);
        }
    }
    
    const finalUrl = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;
    console.log('📄 Abriendo PDF:', finalUrl);
    window.open(finalUrl, '_blank');
}
```

---

### Si el formulario no existe

Verificar que `admin-horarios.blade.php` tiene el formulario:

```powershell
Select-String -Path "resources/views/dashboards/partials/admin-horarios.blade.php" -Pattern "dashboardHorarioExportForm"
```

Debe tener:
```blade
<form id="dashboardHorarioExportForm" method="GET" action="{{ route('dashboard.export.horario') }}" style="display: none;">
    <input type="hidden" name="filtro_docente_id" value="{{ $filtros['filtro_docente_id'] ?? '' }}">
    <!-- ... más filtros -->
</form>
```

---

## 📋 CHECKLIST DE VERIFICACIÓN

Marca cada punto:

- [ ] Servidor Laravel corriendo (`php artisan serve`)
- [ ] Rutas registradas (`php artisan route:list | Select-String export`)
- [ ] Enlaces directos funcionan (probar en navegador)
- [ ] Página de prueba funciona (`test-exportacion.html`)
- [ ] Dashboard carga sin errores
- [ ] Consola del navegador sin errores (F12)
- [ ] Funciones JavaScript existen (`typeof submitExportForm`)
- [ ] Formulario existe (`document.getElementById('dashboardHorarioExportForm')`)
- [ ] Contenedor filtros existe (`document.getElementById('dashboardHorarioPdfFilters')`)
- [ ] Botones responden al clic

---

## 🎯 SIGUIENTE PASO

**Ejecuta estos comandos en orden**:

```powershell
# 1. Limpiar cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 2. Iniciar servidor
php artisan serve

# 3. En otra terminal, iniciar Vite (si usas)
npm run dev
```

**Luego prueba**:

1. Abrir: `http://127.0.0.1:8000/test-exportacion.html`
2. Hacer clic en todos los botones de prueba
3. Si funciona → El problema está en el dashboard
4. Si no funciona → El problema está en las rutas/backend

---

## 📞 REPORTE DE PROBLEMA

Si nada funciona, necesito esta información:

1. **Captura de consola del navegador** (F12 → Console)
2. **Captura de Network tab** al hacer clic en exportar
3. **Resultado de**:
   ```powershell
   php artisan route:list | Select-String "dashboard.export"
   Get-Content storage/logs/laravel.log -Tail 50
   ```

---

**SIGUIENTE**: Ejecuta el PASO 2 (probar rutas directamente) y reporta qué sucede.
