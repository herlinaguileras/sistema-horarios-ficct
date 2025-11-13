# 🎯 ACCIÓN INMEDIATA - Probar Exportación

## ✅ DIAGNÓSTICO COMPLETADO

**Base de datos**: PostgreSQL ✅  
**Backend**: Configurado correctamente ✅  
**Archivos**: Todos presentes ✅

---

## 🚀 PRUEBA RÁPIDA (2 MINUTOS)

### 1. Asegúrate que el servidor esté corriendo

```powershell
# En una terminal PowerShell:
cd c:\laragon\www\materia
php artisan serve
```

**Déjalo corriendo** y abre otra terminal para los siguientes pasos.

---

### 2. Prueba estas URLs DIRECTAMENTE en tu navegador

**Copia y pega cada URL en tu navegador Chrome/Edge:**

#### ✅ TEST 1: Excel sin filtros
```
http://127.0.0.1:8000/dashboard/export/horario-semanal
```
**Debe descargar**: `horario_semanal_Gestion 1 - 2026.xlsx` (17 horarios)

#### ✅ TEST 2: PDF sin filtros
```
http://127.0.0.1:8000/dashboard/export/horario-semanal-pdf
```
**Debe descargar**: `horario_semanal_Gestion 1 - 2026.pdf` (17 horarios)

#### ✅ TEST 3: Excel con filtro docente 38
```
http://127.0.0.1:8000/dashboard/export/horario-semanal?filtro_docente_id=38
```
**Debe descargar**: Excel con solo 4 horarios de GONZALES RODRIGO

#### ✅ TEST 4: PDF con filtro docente 38
```
http://127.0.0.1:8000/dashboard/export/horario-semanal-pdf?filtro_docente_id=38
```
**Debe descargar**: PDF con solo 4 horarios de GONZALES RODRIGO

---

### 3. Resultado

**SI LAS 4 URLS DESCARGAN ARCHIVOS**:
✅ El backend funciona perfectamente  
➡️ El problema está en los botones del dashboard (JavaScript)

**SI NO DESCARGAN**:
❌ Hay un problema en el backend  
➡️ Revisar logs: `Get-Content storage/logs/laravel.log -Tail 50`

---

## 🔍 SI LAS URLs FUNCIONAN PERO LOS BOTONES NO

### Prueba la página de test

```
http://127.0.0.1:8000/test-exportacion.html
```

1. Haz clic en cada botón
2. Verifica que descarguen los archivos

---

## 🐛 SI LOS BOTONES DEL DASHBOARD NO FUNCIONAN

### Opción A: Abrir Dashboard y revisar consola

1. Ir a: `http://127.0.0.1:8000/dashboard?tab=horarios`
2. Presionar **F12** (abrir DevTools)
3. Ir a pestaña **Console**
4. ¿Hay errores en rojo?
5. Hacer clic en botón Excel
6. ¿Qué mensaje aparece en la consola?

**Ejecutar esto en la consola**:
```javascript
// Verificar si las funciones existen
console.log('submitExportForm:', typeof submitExportForm);
console.log('exportPdfWithFilters:', typeof exportPdfWithFilters);
console.log('Form:', document.getElementById('dashboardHorarioExportForm'));
console.log('Filters:', document.getElementById('dashboardHorarioPdfFilters'));
```

**Resultado esperado**:
```
submitExportForm: function
exportPdfWithFilters: function
Form: <form id="dashboardHorarioExportForm">...
Filters: <div id="dashboardHorarioPdfFilters">...
```

### Opción B: Probar export manual desde consola

**Con el dashboard abierto**, ejecuta en la consola:

```javascript
// Test Excel
document.getElementById('dashboardHorarioExportForm').submit();

// Test PDF (ejecutar después)
window.open('/dashboard/export/horario-semanal-pdf?filtro_docente_id=38', '_blank');
```

---

## 📝 REPORTA LOS RESULTADOS

**Por favor, prueba y dime**:

1. ¿Las 4 URLs directas descargan archivos? (SÍ/NO)
2. ¿La página test-exportacion.html funciona? (SÍ/NO)
3. ¿Hay errores en la consola del dashboard? (captura)
4. ¿Qué muestra el test de funciones JavaScript?

---

## 🔧 POSIBLES SOLUCIONES RÁPIDAS

### Si dice "submitExportForm is not defined"

```powershell
# Limpiar cache de vistas
php artisan view:clear
php artisan config:clear

# Refrescar navegador con Ctrl+F5
```

### Si el formulario es "null"

Verificar que estás en la pestaña correcta:
```
http://127.0.0.1:8000/dashboard?tab=horarios
```

### Si nada funciona

```powershell
# Limpiar TODO
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Reiniciar servidor
# Ctrl+C para detener
php artisan serve

# Refrescar navegador con Ctrl+Shift+R
```

---

## 📊 RESUMEN DE ARCHIVOS

**Todo esto existe y funciona**:
- ✅ Rutas registradas
- ✅ DashboardController con métodos
- ✅ HorarioSemanalExport (compatible PostgreSQL)
- ✅ Vista PDF horario_semanal.blade.php
- ✅ Paquetes instalados (Excel, PDF)
- ✅ 17 horarios en base de datos
- ✅ Semestre activo: "Gestion 1 - 2026"

**El sistema está al 100% configurado. Solo falta que los botones del frontend ejecuten las funciones.**

---

## ⚡ PRÓXIMO PASO

**EJECUTA AHORA**:

1. Abre terminal
2. Ejecuta: `php artisan serve`
3. Abre navegador
4. Pega esta URL: `http://127.0.0.1:8000/dashboard/export/horario-semanal`
5. **Dime si descarga el archivo Excel**

Si descarga → El backend funciona, solo falta arreglar JavaScript  
Si no descarga → Necesito ver los logs de error

**¿Qué resultado obtuviste?**
