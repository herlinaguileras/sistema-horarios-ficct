# TEST DE EXPORTACIÓN DASHBOARD - CHECKLIST

**Fecha**: 13 de Noviembre de 2025  
**URL**: `http://127.0.0.1:8000/dashboard?tab=horarios&filtro_docente_id=38`

---

## ✅ VERIFICACIONES COMPLETADAS AUTOMÁTICAMENTE

### 1. Rutas Registradas ✅
```
✅ dashboard.export.horario           → DashboardController@exportHorarioSemanal
✅ dashboard.export.horario.pdf       → DashboardController@exportHorarioSemanalPdf
✅ dashboard.export.asistencia        → DashboardController@exportAsistencia
✅ dashboard.export.asistencia.pdf    → DashboardController@exportAsistenciaPdf
✅ audit-logs.export                  → AuditLogController@export (SEPARADO)
```

### 2. Archivos Existentes ✅
```
✅ app/Exports/HorarioSemanalExport.php
✅ app/Exports/AsistenciaExport.php
✅ resources/views/pdf/horario_semanal.blade.php
✅ resources/views/pdf/asistencia.blade.php
```

### 3. Separación Dashboard vs Bitácora ✅
```
✅ Rutas diferentes (dashboard/export/* vs audit-logs/export)
✅ Controladores diferentes (DashboardController vs AuditLogController)
✅ Modelos diferentes (Horario/Asistencia vs AuditLog)
✅ Sin conflicto de métodos
```

---

## 🧪 PRUEBAS MANUALES A REALIZAR

### PASO 1: Abrir Dashboard
```bash
URL: http://127.0.0.1:8000/dashboard?tab=horarios&filtro_docente_id=38
```

**Verificar:**
- [ ] La página carga correctamente
- [ ] Se muestra la pestaña "Horario Semanal"
- [ ] Los filtros están aplicados (Docente ID: 38)
- [ ] Se muestran horarios filtrados

---

### PASO 2: Probar Botón Excel

**Acciones:**
1. [ ] Localizar botón verde "📊 EXCEL"
2. [ ] Hacer clic en el botón
3. [ ] **Observar:** Botón cambia a "Exportando..."
4. [ ] **Esperar:** 2-3 segundos
5. [ ] **Verificar:** Se descarga archivo

**Validaciones:**
- [ ] Nombre archivo: `horario_semanal_[nombre_semestre].xlsx`
- [ ] Archivo se descarga completamente
- [ ] Botón vuelve a estado normal

**Abrir archivo Excel y verificar:**
- [ ] Tiene encabezados correctos
- [ ] Contiene solo horarios del docente ID 38
- [ ] Datos son correctos (materia, grupo, horario, aula)
- [ ] Sin errores de codificación (tildes, ñ)

---

### PASO 3: Probar Botón PDF

**Acciones:**
1. [ ] Localizar botón rojo "📄 PDF"
2. [ ] Hacer clic en el botón
3. [ ] **Observar:** Se abre nueva pestaña
4. [ ] **Esperar:** Generación del PDF
5. [ ] **Verificar:** Se descarga archivo

**Validaciones:**
- [ ] Nombre archivo: `horario_semanal_[nombre_semestre].pdf`
- [ ] PDF se genera correctamente
- [ ] Nueva pestaña se abre

**Abrir archivo PDF y verificar:**
- [ ] Formato visual correcto
- [ ] Tablas organizadas por día
- [ ] Contiene solo horarios del docente ID 38
- [ ] Todos los datos legibles
- [ ] Sin caracteres raros

---

### PASO 4: Probar Sin Filtros

**Acciones:**
1. [ ] Click en botón "Limpiar"
2. [ ] Verificar que se eliminan todos los filtros
3. [ ] Click en "Filtrar"
4. [ ] Exportar Excel
5. [ ] Exportar PDF

**Validaciones Excel:**
- [ ] Contiene TODOS los horarios del semestre
- [ ] No solo del docente 38
- [ ] Múltiples docentes presentes

**Validaciones PDF:**
- [ ] Contiene TODOS los horarios del semestre
- [ ] Múltiples docentes presentes
- [ ] Organizado por días

---

### PASO 5: Probar Múltiples Filtros

**Aplicar filtros:**
- Docente: [Seleccionar uno]
- Materia: [Seleccionar una]
- Día: Lunes

**Acciones:**
1. [ ] Aplicar filtros
2. [ ] Exportar Excel
3. [ ] Exportar PDF

**Validaciones:**
- [ ] Excel solo tiene registros que cumplen TODOS los filtros
- [ ] PDF solo tiene registros que cumplen TODOS los filtros
- [ ] Datos consistentes entre Excel y PDF

---

### PASO 6: Verificar Pestaña Asistencias

**URL:**
```bash
http://127.0.0.1:8000/dashboard?tab=asistencias
```

**Acciones:**
1. [ ] Cambiar a pestaña "Asistencia Docente/Grupo"
2. [ ] Verificar botones Excel y PDF visibles
3. [ ] Click en Excel
4. [ ] Click en PDF

**Validaciones Excel:**
- [ ] Descarga: `asistencia_[semestre].xlsx`
- [ ] Columnas: Docente, Materia, Grupo, Fecha, Hora, Estado, Método
- [ ] Datos correctos

**Validaciones PDF:**
- [ ] Descarga: `asistencia_[semestre].pdf`
- [ ] Agrupado por docente y grupo
- [ ] Formato legible

---

### PASO 7: Verificar Consola del Navegador

**Abrir consola (F12 → Console):**

```javascript
// Ejecutar estos comandos uno por uno:

// 1. Verificar formulario Excel existe
console.log('Form Excel:', document.getElementById('dashboardHorarioExportForm'));

// 2. Verificar contenedor filtros PDF existe
console.log('Filtros PDF:', document.getElementById('dashboardHorarioPdfFilters'));

// 3. Verificar action del formulario
const form = document.getElementById('dashboardHorarioExportForm');
console.log('Action:', form?.action);

// 4. Verificar dataset de filtros
const filters = document.getElementById('dashboardHorarioPdfFilters');
console.log('Dataset:', filters?.dataset);

// 5. Verificar funciones globales existen
console.log('submitExportForm:', typeof submitExportForm);
console.log('exportPdfWithFilters:', typeof exportPdfWithFilters);
```

**Resultados esperados:**
- [ ] Formulario encontrado: `<form id="dashboardHorarioExportForm">`
- [ ] Filtros encontrados: `<div id="dashboardHorarioPdfFilters">`
- [ ] Action correcto: termina en `/dashboard/export/horario-semanal`
- [ ] Dataset tiene atributos: `filtro_docente_id`, `filtro_materia_id`, etc.
- [ ] Funciones tipo: `function`

---

### PASO 8: Verificar Bitácora

**URL:**
```bash
http://127.0.0.1:8000/audit-logs
```

**Acciones:**
1. [ ] Acceder a módulo Bitácora
2. [ ] Buscar acción: "export"
3. [ ] Verificar últimas exportaciones

**Validaciones:**
- [ ] Se registró exportación de horario Excel
- [ ] Se registró exportación de horario PDF
- [ ] Action: "export"
- [ ] Model type: "horario_semanal"
- [ ] Details contiene: format, semestre, filters

---

### PASO 9: Test con CURL (Opcional)

```powershell
# Test Excel Horarios
Invoke-WebRequest -Uri "http://127.0.0.1:8000/dashboard/export/horario-semanal?filtro_docente_id=38" -OutFile "test_horario.xlsx"

# Test PDF Horarios
Invoke-WebRequest -Uri "http://127.0.0.1:8000/dashboard/export/horario-semanal-pdf?filtro_docente_id=38" -OutFile "test_horario.pdf"

# Test Excel Asistencias
Invoke-WebRequest -Uri "http://127.0.0.1:8000/dashboard/export/asistencia" -OutFile "test_asistencia.xlsx"

# Test PDF Asistencias
Invoke-WebRequest -Uri "http://127.0.0.1:8000/dashboard/export/asistencia-pdf" -OutFile "test_asistencia.pdf"
```

**Validaciones:**
- [ ] Todos los archivos se descargan
- [ ] Sin errores 404 o 500
- [ ] Archivos tienen contenido válido

---

### PASO 10: Test de Errores

**Escenario: Sin semestre activo**

**Acciones (en Tinker):**
```php
// Desactivar semestre
$semestre = \App\Models\Semestre::where('estado', 'Activo')->first();
$semestre->update(['estado' => 'Inactivo']);
```

**En navegador:**
1. [ ] Intentar exportar Excel
2. [ ] Intentar exportar PDF

**Validaciones:**
- [ ] Muestra mensaje: "No hay un semestre activo para exportar"
- [ ] No se descarga archivo
- [ ] No hay error 500

**Restaurar (en Tinker):**
```php
$semestre->update(['estado' => 'Activo']);
```

---

## 📊 MATRIZ DE RESULTADOS

| Test | Estado | Observaciones |
|------|--------|---------------|
| Rutas registradas | ✅ | - |
| Archivos existen | ✅ | - |
| Botón Excel Horarios | ⬜ | - |
| Botón PDF Horarios | ⬜ | - |
| Excel sin filtros | ⬜ | - |
| PDF sin filtros | ⬜ | - |
| Excel con filtros | ⬜ | - |
| PDF con filtros | ⬜ | - |
| Botón Excel Asistencias | ⬜ | - |
| Botón PDF Asistencias | ⬜ | - |
| Consola JavaScript | ⬜ | - |
| Registro en bitácora | ⬜ | - |
| Test CURL | ⬜ | - |
| Manejo de errores | ⬜ | - |

**Leyenda:**
- ✅ Pasó
- ❌ Falló
- ⬜ Pendiente
- ⚠️ Con observaciones

---

## 🐛 PROBLEMAS ENCONTRADOS

### Problema 1: [Descripción]
**Síntoma:**
**Causa:**
**Solución:**

### Problema 2: [Descripción]
**Síntoma:**
**Causa:**
**Solución:**

---

## ✅ VALIDACIÓN FINAL

**Criterios Mínimos:**
- [ ] Excel Horarios funciona
- [ ] PDF Horarios funciona
- [ ] Filtros se aplican correctamente
- [ ] Sin errores en consola
- [ ] Archivos con contenido válido
- [ ] Registros en bitácora

**Estado General:**
- [ ] ✅ Todos los tests pasaron
- [ ] ⚠️ Algunos tests con observaciones
- [ ] ❌ Tests fallaron - requiere corrección

---

## 📝 CONCLUSIONES

[Escribir conclusiones después de ejecutar todos los tests]

**Funcionamiento:**
- Exportación Excel: [OK/FAIL]
- Exportación PDF: [OK/FAIL]
- Aplicación de filtros: [OK/FAIL]
- Separación de bitácora: [OK/FAIL]

**Recomendaciones:**
1. [Si aplica]
2. [Si aplica]

---

## 🔧 ACCIONES CORRECTIVAS

[Si se encontraron problemas, listar acciones necesarias]

1. [ ] Acción 1
2. [ ] Acción 2
3. [ ] Acción 3
