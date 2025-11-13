# GUÍA RÁPIDA - PRUEBAS DE EXPORTACIÓN DASHBOARD

**Fecha**: 13 de Noviembre de 2025  
**Tiempo estimado**: 10 minutos

---

## 🚀 INICIO RÁPIDO

### Pre-requisitos
- ✅ Servidor Laravel corriendo (`php artisan serve`)
- ✅ Base de datos con datos de prueba
- ✅ Semestre activo en la BD
- ✅ Usuario autenticado con permisos

---

## 📋 PRUEBAS BÁSICAS (5 MINUTOS)

### 1. Acceder al Dashboard
```
URL: http://127.0.0.1:8000/dashboard?tab=horarios&filtro_docente_id=38
```

### 2. Probar Botón Excel
1. Click en botón verde "📊 EXCEL"
2. **Esperar 2-3 segundos**
3. **Verificar**: Se descarga `horario_semanal_[semestre].xlsx`

✅ **PASS** si el archivo se descarga  
❌ **FAIL** si hay error o no descarga

### 3. Probar Botón PDF
1. Click en botón rojo "📄 PDF"
2. **Esperar 2-3 segundos**
3. **Verificar**: Se abre nueva pestaña y descarga PDF

✅ **PASS** si se abre pestaña y descarga  
❌ **FAIL** si hay error o no descarga

### 4. Verificar Contenido
1. Abrir archivo Excel descargado
2. Verificar que solo tiene horarios del docente ID 38
3. Abrir archivo PDF descargado
4. Verificar que solo tiene horarios del docente ID 38

✅ **PASS** si los filtros se aplicaron correctamente  
❌ **FAIL** si muestra todos los horarios

---

## 🔍 PRUEBAS INTERMEDIAS (3 MINUTOS)

### 5. Exportar Sin Filtros
```
URL: http://127.0.0.1:8000/dashboard?tab=horarios
```

1. Click en "Limpiar" filtros
2. Exportar Excel
3. Exportar PDF
4. Verificar que contienen TODOS los horarios del semestre

### 6. Exportar Asistencias
```
URL: http://127.0.0.1:8000/dashboard?tab=asistencias
```

1. Cambiar a pestaña "Asistencia Docente/Grupo"
2. Click en botón Excel
3. Click en botón PDF
4. Verificar descargas

---

## 🧪 VERIFICACIÓN CONSOLA (2 MINUTOS)

### 7. Abrir Consola del Navegador
**Presionar F12 → Pestaña Console**

Ejecutar:
```javascript
// Verificar formulario
console.log('Form:', document.getElementById('dashboardHorarioExportForm'));

// Verificar filtros
console.log('Filters:', document.getElementById('dashboardHorarioPdfFilters'));

// Verificar funciones
console.log('submitExportForm:', typeof submitExportForm);
console.log('exportPdfWithFilters:', typeof exportPdfWithFilters);
```

**Resultado esperado:**
- Formulario: `<form id="dashboardHorarioExportForm">`
- Filtros: `<div id="dashboardHorarioPdfFilters">`
- Funciones: `function`

✅ **PASS** si todo se muestra correctamente  
❌ **FAIL** si algo es `null` o `undefined`

---

## 📊 VERIFICACIÓN EN BITÁCORA

### 8. Comprobar Registro
```
URL: http://127.0.0.1:8000/audit-logs
```

1. Acceder al módulo Bitácora
2. Buscar últimos registros
3. Verificar acciones "export"
4. Ver detalles del registro

**Campos esperados:**
- Action: `export`
- Model Type: `horario_semanal`
- Details: `{"format":"xlsx|pdf", "semestre":"...", "filters":{...}}`

---

## 🔧 VERIFICACIÓN TÉCNICA (OPCIONAL)

### 9. Verificar Rutas
```powershell
php artisan route:list | Select-String "dashboard.export"
```

**Debe mostrar:**
- `dashboard.export.horario` → GET /dashboard/export/horario-semanal
- `dashboard.export.horario.pdf` → GET /dashboard/export/horario-semanal-pdf
- `dashboard.export.asistencia` → GET /dashboard/export/asistencia
- `dashboard.export.asistencia.pdf` → GET /dashboard/export/asistencia-pdf

### 10. Ejecutar Tests
```powershell
php artisan test --filter ExportDashboard
```

**Resultado esperado:**
```
PASS  Tests\Feature\ExportDashboardAdminTest
PASS  Tests\Feature\ExportacionDashboardTest

Tests:  X passed
Time:   Xs
```

---

## ✅ CHECKLIST RÁPIDO

- [ ] Excel Horarios descarga
- [ ] PDF Horarios descarga
- [ ] Filtros se aplican correctamente
- [ ] Excel Asistencias descarga
- [ ] PDF Asistencias descarga
- [ ] Consola sin errores
- [ ] Formularios existen
- [ ] Funciones JavaScript existen
- [ ] Registros en bitácora
- [ ] Tests pasan

---

## 🐛 SOLUCIÓN DE PROBLEMAS

### Problema: No descarga Excel
**Solución:**
1. Verificar que existe `HorarioSemanalExport.php`
2. Verificar imports en controlador:
   ```bash
   cat app/Http/Controllers/DashboardController.php | Select-String "Excel"
   ```
3. Verificar extensión instalada:
   ```bash
   composer show | Select-String "excel"
   ```

### Problema: No descarga PDF
**Solución:**
1. Verificar que existe vista `resources/views/pdf/horario_semanal.blade.php`
2. Verificar imports:
   ```bash
   cat app/Http/Controllers/DashboardController.php | Select-String "Pdf"
   ```
3. Verificar extensión:
   ```bash
   composer show | Select-String "dompdf"
   ```

### Problema: Botón no responde
**Solución:**
1. Abrir consola (F12)
2. Buscar errores en rojo
3. Verificar que funciones existen:
   ```javascript
   typeof submitExportForm
   typeof exportPdfWithFilters
   ```

### Problema: Error "No hay semestre activo"
**Solución:**
```bash
php artisan tinker
>>> $s = \App\Models\Semestre::first();
>>> $s->update(['estado' => 'Activo']);
>>> exit
```

---

## 📝 RESULTADO FINAL

### Si todos los tests pasaron:
✅ **SISTEMA FUNCIONANDO CORRECTAMENTE**

**No se requiere ninguna corrección.**

### Si algún test falló:
⚠️ **REVISAR DOCUMENTACIÓN COMPLETA**

Consultar:
- `PLAN_VERIFICACION_EXPORTACION_DASHBOARD.md`
- `TEST_EXPORTACION_DASHBOARD.md`
- `RESUMEN_EJECUTIVO_EXPORTACION.md`

---

## 📞 COMANDOS ÚTILES

```powershell
# Ver rutas
php artisan route:list | Select-String "export"

# Limpiar cache
php artisan config:clear; php artisan route:clear; php artisan view:clear

# Ver logs
Get-Content storage/logs/laravel.log -Tail 50

# Ejecutar tests
php artisan test --filter Export

# Ver semestre activo
php artisan tinker
>>> \App\Models\Semestre::where('estado', 'Activo')->first()

# Verificar paquetes
composer show | Select-String "excel|dompdf"
```

---

## 🎯 TIEMPO TOTAL ESTIMADO

- ✅ Pruebas básicas: **5 minutos**
- ✅ Pruebas intermedias: **3 minutos**
- ✅ Verificación consola: **2 minutos**
- ✅ Verificación técnica: **5 minutos** (opcional)

**Total: 10-15 minutos**

---

**Última actualización**: 13 de Noviembre de 2025
