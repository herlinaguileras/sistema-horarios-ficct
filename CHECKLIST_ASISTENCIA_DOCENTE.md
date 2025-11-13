# ✅ CHECKLIST IMPLEMENTACIÓN ASISTENCIA DOCENTE

**Proyecto:** Sistema de Horarios FICCT  
**Módulo:** Asistencia de Docentes con QR  
**Fecha Inicio:** 12 de Noviembre, 2025

---

## 📋 PROGRESO GENERAL

```
[███░░░░░░░] 30% - En progreso
```

---

## FASE 1: Verificación de Métodos Existentes ⏱️ 10 min

- [x] ✅ Revisar AsistenciaController
  - [x] Método `generarQR()` existe
  - [ ] Método `escanearQR()` existe - **FALTA IMPLEMENTAR**
  
- [ ] Verificar rutas actuales
  - [x] Ruta `asistencias.qr.generar` existe
  - [ ] Ruta `asistencias.qr.scan` existe - **FALTA AGREGAR**

- [x] ✅ Verificar base de datos
  - [x] NO hay campos de estudiantes ✅
  - [x] Estructura correcta para docentes ✅

---

## FASE 2: Implementar Método escanearQR() ⏱️ 45 min

### Backend - AsistenciaController.php

- [ ] Agregar imports necesarios
  ```php
  use Illuminate\Support\Facades\URL;
  use Carbon\Carbon;
  ```

- [ ] Implementar validación de firma
  - [ ] `$request->hasValidSignature()`
  - [ ] Vista de error: `qr-expired`

- [ ] Implementar desencriptación de token
  - [ ] `decrypt($token)`
  - [ ] Manejo de excepciones
  - [ ] Vista de error: `qr-unauthorized`

- [ ] Implementar validación de docente autorizado
  - [ ] `$docenteId == $horario->docente_id`
  - [ ] Vista de error: `qr-unauthorized`

- [ ] Implementar validación de ventana de tiempo
  - [ ] Obtener hora actual
  - [ ] Calcular ventana ±15 minutos
  - [ ] Comparar con horario del grupo
  - [ ] Vista de error: `qr-time-window`

- [ ] Implementar verificación de duplicados
  - [ ] Query: `where(horario_id, fecha, docente_id)`
  - [ ] Mensaje informativo si ya existe

- [ ] Implementar creación de asistencia
  - [ ] `Asistencia::create()`
  - [ ] Campos: horario_id, docente_id, fecha, hora_registro, estado, metodo_registro
  - [ ] Vista de éxito: `qr-success`

**Archivo:** `app/Http/Controllers/AsistenciaController.php`

---

## FASE 3: Actualizar Método generarQR() ⏱️ 20 min

- [ ] Mejorar validaciones de seguridad
  - [ ] Validar `Auth::user()->docente`
  - [ ] Validar propiedad del horario
  - [ ] Abort 403 si no autorizado

- [ ] Encriptar token con ID docente
  - [ ] `encrypt($docente->id)`

- [ ] Generar URL firmada temporal
  - [ ] `URL::temporarySignedRoute()`
  - [ ] Expiración: 1 hora
  - [ ] Parámetros: horario, token

- [ ] Mejorar generación QR
  - [ ] Formato: SVG
  - [ ] Tamaño: 300px
  - [ ] Error correction: H

**Archivo:** `app/Http/Controllers/AsistenciaController.php`

---

## FASE 4: Crear Vistas de Error ⏱️ 30 min

### 4.1. Vista: qr-expired.blade.php
- [ ] Crear archivo
- [ ] Layout base
- [ ] Icono de reloj
- [ ] Mensaje de expiración
- [ ] Botón volver a horarios

**Archivo:** `resources/views/errors/qr-expired.blade.php`

### 4.2. Vista: qr-unauthorized.blade.php
- [ ] Crear archivo
- [ ] Layout base
- [ ] Icono de prohibido
- [ ] Mensaje de no autorizado
- [ ] Botón volver a dashboard

**Archivo:** `resources/views/errors/qr-unauthorized.blade.php`

### 4.3. Vista: qr-time-window.blade.php
- [ ] Crear archivo
- [ ] Layout base
- [ ] Icono de advertencia
- [ ] Mostrar hora actual
- [ ] Mostrar ventana permitida
- [ ] Botón volver a horarios

**Archivo:** `resources/views/errors/qr-time-window.blade.php`

---

## FASE 5: Crear Vista de Éxito ⏱️ 20 min

### 5.1. Vista: qr-success.blade.php
- [ ] Crear archivo
- [ ] Layout base
- [ ] Icono de éxito
- [ ] Mensaje dinámico (éxito/info)
- [ ] Mostrar fecha y hora
- [ ] Botones: Mis Horarios, Dashboard

**Archivo:** `resources/views/docente/qr-success.blade.php`

---

## FASE 6: Agregar Rutas Faltantes ⏱️ 10 min

- [ ] Agregar ruta de escaneo QR
  ```php
  Route::get('/asistencias/qr-scan/{horario}/{token}', 
      [AsistenciaController::class, 'escanearQR'])
      ->name('asistencias.qr.scan');
  ```

- [ ] Verificar grupo de middleware correcto
- [ ] Probar ruta con `php artisan route:list`

**Archivo:** `routes/web.php`

---

## FASE 7: Testing y Validación ⏱️ 30 min

### 7.1. Tests Automatizados

- [ ] Crear archivo de test
- [ ] Test: Generar QR propio
- [ ] Test: No generar QR ajeno
- [ ] Test: QR expirado muestra error
- [ ] Test: Asistencia se registra correctamente
- [ ] Test: No permite duplicados
- [ ] Test: No permite registro fuera de tiempo

**Archivo:** `tests/Feature/AsistenciaDocenteTest.php`

### 7.2. Tests Manuales

- [ ] **Test 1:** Generar QR desde web (docente propietario)
  - Ruta: `/docente/horarios`
  - Acción: Click en "Generar QR"
  - Esperado: Modal con QR

- [ ] **Test 2:** Intentar generar QR ajeno
  - Acción: Modificar URL con ID de horario ajeno
  - Esperado: Error 403

- [ ] **Test 3:** Escanear QR válido (móvil)
  - Acción: Escanear QR recién generado
  - Esperado: Vista de éxito + registro en BD

- [ ] **Test 4:** Escanear QR expirado
  - Acción: Esperar 1 hora + escanear
  - Esperado: Vista `qr-expired`

- [ ] **Test 5:** Escanear QR con token manipulado
  - Acción: Modificar parámetro token en URL
  - Esperado: Vista `qr-unauthorized`

- [ ] **Test 6:** Escanear QR fuera de horario
  - Acción: Escanear QR cuando no corresponde
  - Esperado: Vista `qr-time-window`

- [ ] **Test 7:** Escanear QR con asistencia previa
  - Acción: Escanear QR dos veces mismo día
  - Esperado: Mensaje "Ya registrada"

- [ ] **Test 8:** Verificar que NO hay estudiantes
  - Acción: Revisar tabla `asistencias`
  - Esperado: Solo registros de docentes

---

## 🔍 VALIDACIÓN FINAL

### Criterios de Aceptación

- [ ] ✅ Docente puede generar QR solo de sus horarios
- [ ] ✅ QR tiene validez de 1 hora
- [ ] ✅ Solo el docente propietario puede escanear
- [ ] ✅ Validación de ventana de tiempo (±15 min)
- [ ] ✅ No permite duplicados mismo día
- [ ] ✅ Registro correcto: estado "Presente", método "QR"
- [ ] ✅ **Sin referencias a estudiantes**
- [ ] ✅ Vistas de error amigables
- [ ] ✅ URLs firmadas y seguras
- [ ] ✅ Tests automatizados pasan
- [ ] ✅ Código limpio y documentado

---

## 📊 ARCHIVOS A MODIFICAR/CREAR

### Modificar (2):
- [ ] `app/Http/Controllers/AsistenciaController.php`
- [ ] `routes/web.php`

### Crear (5):
- [ ] `resources/views/errors/qr-expired.blade.php`
- [ ] `resources/views/errors/qr-unauthorized.blade.php`
- [ ] `resources/views/errors/qr-time-window.blade.php`
- [ ] `resources/views/docente/qr-success.blade.php`
- [ ] `tests/Feature/AsistenciaDocenteTest.php`

---

## 🐛 ISSUES ENCONTRADOS

### Durante Implementación:
- [ ] (Agregar aquí cualquier problema encontrado)

### Soluciones Aplicadas:
- [ ] (Documentar soluciones)

---

## 📝 NOTAS DEL DESARROLLADOR

```
[Agregar aquí notas durante la implementación]
```

---

## ✅ APROBACIÓN FINAL

- [ ] Code review completado
- [ ] Tests pasando (100%)
- [ ] Documentación actualizada
- [ ] Deploy a staging
- [ ] Validación usuario final
- [ ] Deploy a producción

---

**Última actualización:** 12 de Noviembre, 2025  
**Responsable:** Sistema de Horarios FICCT  
**Estado:** 🔄 En Progreso
