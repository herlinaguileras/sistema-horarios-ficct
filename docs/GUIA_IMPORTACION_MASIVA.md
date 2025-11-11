# 📊 MÓDULO DE IMPORTACIÓN MASIVA DE HORARIOS

> **Fecha de creación**: <?= date('Y-m-d') ?>  
> **Versión**: 1.0  
> **Estado**: Implementado

---

## 🎯 OBJETIVO

Permitir la importación masiva de horarios desde archivos Excel o CSV, automatizando la creación de docentes, materias, grupos y horarios con validaciones completas.

---

## 📋 CARACTERÍSTICAS

### ✅ Funcionalidades Principales

1. **Importación desde Excel/CSV**
   - Soporte para archivos .xlsx, .xls, .csv
   - Tamaño máximo: 10MB
   - Procesamiento por lotes

2. **Creación Automática**
   - ✓ Docentes (con usuario y correo autogenerado)
   - ✓ Materias (si no existen)
   - ✓ Grupos (asociando materia y docente)
   - ✓ Horarios (con validación de conflictos)

3. **Actualización Inteligente**
   - Sobrescribe datos existentes de forma segura
   - Mantiene integridad referencial
   - Valida antes de actualizar

4. **Validaciones Completas**
   - ✓ Choque de horarios en aulas
   - ✓ Aulas existentes en el sistema
   - ✓ Formato de datos correcto
   - ✓ Rangos horarios válidos
   - ✓ Días de la semana correctos

---

## 📝 FORMATO DEL ARCHIVO

### Columnas Requeridas

| Columna | Descripción | Ejemplo | Obligatorio |
|---------|-------------|---------|-------------|
| **SIGLA** | Código de la materia | MAT101 | ✓ |
| **SEMESTRE** | Nivel del semestre | 1, 2, 3... | ✓ |
| **GRUPO** | Nombre del grupo | F1, SZ, CI | ✓ |
| **MATERIA** | Nombre completo | CALCULO I | ✓ |
| **DOCENTE** | Nombre completo | AVENDAÑO GONZALES EUDAL | ✓ |
| **CI** | Cédula de identidad | 1234567 | ✓ |
| **TELEFONO** | Teléfono de contacto | 70123456 | Opcional |
| **DIA** | Día de la clase | Mar, Mie, Jue... | ✓ |
| **HORA** | Rango horario | 18:15-20:30 | ✓ |
| **AULA** | Número del aula | 14, 22, 33... | ✓ |

**Nota**: Las columnas DIA-HORA-AULA se pueden repetir hasta 4 veces para múltiples horarios del mismo grupo.

### Ejemplo de Datos

```
SIGLA | SEMESTRE | GRUPO | MATERIA    | DOCENTE                 | CI      | TELEFONO | DIA | HORA        | AULA | DIA | HORA        | AULA
------|----------|-------|------------|-------------------------|---------|----------|-----|-------------|------|-----|-------------|------
MAT101| 1        | F1    | CALCULO I  | AVENDAÑO GONZALES EUDAL | 1234567 | 70123456 | Mar | 18:15-20:30 | 14   | Jue | 18:15-20:30 | 14
MAT101| 1        | SZ    | CALCULO I  | JUSTINIANO VACA JUAN    | 2345678 | 71234567 | Mar | 9:15-11:30  | 12   | Jue | 9:15-11:30  | 12
FIS100| 1        | A     | FISICA I   | RODRIGUEZ PEREZ MARIO   | 5678901 | 74567890 | Mar | 10:30-12:00 | 22   | Vie | 10:30-12:00 | 22
```

---

## 🔧 LÓGICA DE PROCESAMIENTO

### 1. Validación Inicial
```
- Verificar formato del archivo (Excel/CSV válido)
- Validar tamaño (máx 10MB)
- Verificar encabezados correctos
```

### 2. Procesamiento por Fila

#### A. Materia
```php
1. Buscar materia por SIGLA
2. Si NO existe:
   - Crear materia con: nombre, sigla, nivel_semestre
   - Registrar en log: "Materia creada automáticamente"
3. Si SÍ existe:
   - Actualizar nombre si cambió
   - Registrar advertencia si hubo cambios
```

#### B. Docente
```php
1. Buscar docente por CI
2. Si NO existe:
   - Generar código automático (DOC0001, DOC0002...)
   - Generar email: apellido1.apellido2@ficct.edu.bo
   - Crear usuario con:
     * name = nombre completo
     * email = email generado
     * password = "password" (por defecto)
     * is_active = true
   - Asignar rol "docente"
   - Crear registro en tabla docentes
   - Registrar en log: "Docente creado con código DOCXXXX"
3. Si SÍ existe:
   - Actualizar datos si cambiaron (nombre, teléfono)
   - Registrar advertencias de cambios
```

#### C. Grupo
```php
1. Buscar grupo por: nombre + materia_id
2. Si NO existe:
   - Crear grupo asociando materia_id y docente_id
   - Registrar: "Grupo creado"
3. Si SÍ existe:
   - Actualizar docente si cambió
   - Eliminar horarios antiguos (para reemplazar)
```

#### D. Horarios
```php
Para cada par DIA-HORA-AULA:
1. Validar día (Lun, Mar, Mie, Jue, Vie, Sab)
2. Parsear hora (formato: HH:MM-HH:MM)
3. Buscar aula por número
4. Si aula NO existe:
   - Advertencia: "Aula XX no existe - horario omitido"
   - Continuar con siguiente
5. Si aula SÍ existe:
   - VALIDAR CONFLICTOS:
     a) Buscar horarios en misma aula + mismo día
     b) Verificar traslape de horarios
     c) Si hay traslape:
        - Advertencia: "Conflicto con [Materia] [Grupo]"
        - Omitir este horario
   - Si NO hay conflicto:
     - Crear horario
     - Incrementar contador
```

### 3. Generación de Resultados
```
- Contar: procesadas, exitosas, errores, advertencias
- Contar: docentes, materias, grupos, horarios creados
- Generar log detallado
- Mostrar pantalla de resultados
```

---

## 🔒 VALIDACIONES IMPLEMENTADAS

### 1. Campos Requeridos
- ✓ SIGLA, SEMESTRE, GRUPO, MATERIA, DOCENTE obligatorios
- ✓ Mensaje claro si falta alguno

### 2. Formato de Datos
- ✓ Hora: formato "HH:MM-HH:MM"
- ✓ Día: validación contra lista permitida
- ✓ CI: único por docente

### 3. Integridad Referencial
- ✓ Aulas deben existir previamente
- ✓ No se crean aulas automáticamente

### 4. Conflictos de Horarios
```php
Validación de traslape:
- Mismo día
- Misma aula  
- Horario se traslapa

Ejemplo de conflicto:
Horario 1: 18:15-20:30
Horario 2: 19:00-21:00
→ CONFLICTO (19:00 está entre 18:15 y 20:30)

Ejemplo válido:
Horario 1: 18:15-20:30
Horario 2: 20:30-22:00
→ OK (20:30 es fin de uno e inicio de otro)
```

---

## 🎨 INTERFAZ DE USUARIO

### Página de Importación
- **Ruta**: `/importacion-horarios`
- **Elementos**:
  - Botón para descargar plantilla
  - Drag & drop para subir archivo
  - Instrucciones claras
  - Advertencias importantes

### Página de Resultados
- **Estadísticas visuales**:
  - Filas procesadas (azul)
  - Exitosas (verde)
  - Errores (rojo)
  - Advertencias (amarillo)
  
- **Detalles de creación**:
  - Docentes creados
  - Materias creadas
  - Grupos creados
  - Horarios creados

- **Log detallado**:
  - Filtros por tipo (todos, éxitos, errores, advertencias)
  - Número de línea
  - Mensaje descriptivo

---

## 💡 GENERACIÓN AUTOMÁTICA

### 1. Código de Docente
```php
Formato: DOCXXXX
Ejemplo: DOC0001, DOC0002, DOC0003...

Lógica:
1. Obtener último código: SELECT MAX(codigo) FROM docentes
2. Extraer número: substr('DOC0042', 3) = '0042'
3. Incrementar: intval('0042') + 1 = 43
4. Formatear: 'DOC' + str_pad(43, 4, '0') = 'DOC0043'
```

### 2. Email de Docente
```php
Formato: apellido1.apellido2@ficct.edu.bo

Ejemplo:
Nombre: "AVENDAÑO GONZALES EUDAL"
↓
Tomar 2 primeros: ["AVENDAÑO", "GONZALES"]
↓
Minúsculas: ["avendaño", "gonzales"]
↓
Sin tildes: ["avendano", "gonzales"]
↓
Unir con punto: "avendano.gonzales"
↓
Email: "avendano.gonzales@ficct.edu.bo"

Si existe, agregar número:
"avendano.gonzales1@ficct.edu.bo"
"avendano.gonzales2@ficct.edu.bo"
```

### 3. Contraseña por Defecto
```
Todos los docentes creados tienen:
password = "password"

⚠️ IMPORTANTE: 
El docente debe cambiar su contraseña en el primer login
```

---

## 📊 ESTADÍSTICAS DE RESULTADOS

### Tipos de Mensajes

#### ✅ Éxito
```
"Grupo 'F1' - CALCULO I: 2 horario(s) creado(s)"
```

#### ⚠️ Advertencias
```
"Materia 'CALCULO I' creada automáticamente"
"Docente 'JUAN PEREZ' creado con código DOC0015"
"Aula 99 no existe - horario omitido"
"Conflicto: Aula ocupada por FISICA I - Grupo A (18:15-20:30)"
"Formato de hora inválido: 18:15"
```

#### ❌ Errores
```
"Campos requeridos faltantes (SIGLA, SEMESTRE, GRUPO)"
"No se pudieron crear horarios para el grupo 'F1'"
"Error inesperado: [mensaje de excepción]"
```

---

## 🚀 USO DEL MÓDULO

### Paso 1: Descargar Plantilla
```
1. Ir a /importacion-horarios
2. Click en "Descargar Plantilla"
3. Se descarga archivo: plantilla_importacion_horarios.xlsx
```

### Paso 2: Completar Datos
```
1. Abrir plantilla en Excel
2. Completar filas con datos (ver ejemplo incluido)
3. Guardar archivo
```

### Paso 3: Subir Archivo
```
1. En /importacion-horarios
2. Arrastrar archivo o click para seleccionar
3. Click en "Importar Horarios"
4. Esperar procesamiento
```

### Paso 4: Revisar Resultados
```
1. Ver estadísticas generales
2. Revisar elementos creados
3. Filtrar log por tipo de mensaje
4. Verificar advertencias y errores
```

---

## ⚙️ CONFIGURACIÓN

### Requisitos
```
- PHP 8.0+
- Laravel 12.x
- PhpOffice/PhpSpreadsheet
- PostgreSQL
```

### Instalación
```bash
composer require phpoffice/phpspreadsheet
```

### Rutas Registradas
```php
GET  /importacion-horarios                 → Formulario
POST /importacion-horarios/import          → Procesar
GET  /importacion-horarios/plantilla       → Descargar plantilla
```

### Middleware
```
- auth
- verified
- module:horarios
```

---

## 🔍 CASOS DE USO

### Caso 1: Importación Completa Nueva
```
Escenario: Inicio de semestre, sin horarios previos

Resultado:
- Todos los docentes creados
- Todas las materias creadas
- Todos los grupos creados
- Todos los horarios creados
```

### Caso 2: Actualización de Docente
```
Escenario: Docente cambia pero CI es el mismo

Datos:
Antes: "JUAN PEREZ" (CI: 123456)
Ahora: "JUAN PEREZ LOPEZ" (CI: 123456)

Resultado:
- Docente actualizado (nombre)
- Advertencia: "Datos del docente actualizados: nombre"
- Horarios asignados al mismo docente (por CI)
```

### Caso 3: Conflicto de Horarios
```
Escenario: Dos grupos quieren el mismo aula al mismo tiempo

Grupo A: Mar 18:15-20:30 Aula 14
Grupo B: Mar 19:00-21:00 Aula 14

Resultado:
- Grupo A: Horario creado ✓
- Grupo B: Horario omitido ⚠️
- Advertencia: "Conflicto: Aula ocupada por [Grupo A]"
```

### Caso 4: Aula No Existe
```
Escenario: Archivo especifica aula que no está en BD

Datos: Aula 99 (no existe en sistema)

Resultado:
- Horario omitido
- Advertencia: "Aula 99 no existe - horario omitido"
- Grupo creado pero sin ese horario
```

---

## 🛠️ MANTENIMIENTO

### Logs
```
Los logs se muestran en pantalla y NO se guardan en BD.
Para guardar historial, agregar tabla audit_logs.
```

### Performance
```
Archivos grandes (>100 filas):
- Procesamiento puede tardar 10-30 segundos
- Usar loading spinner en frontend
- Considerar procesamiento por lotes para >1000 filas
```

### Errores Comunes
```
1. "Archivo muy grande"
   → Reducir tamaño o aumentar límite en config

2. "Formato inválido"
   → Verificar que sea .xlsx, .xls o .csv válido

3. "Memoria agotada"
   → Aumentar memory_limit en php.ini
```

---

## 📈 MEJORAS FUTURAS

### Versión 1.1
- [ ] Procesamiento asíncrono con colas
- [ ] Guardar historial de importaciones
- [ ] Exportar resultados en PDF
- [ ] Preview antes de importar
- [ ] Validación más estricta de nombres

### Versión 1.2
- [ ] Importación desde Google Sheets
- [ ] API REST para importación
- [ ] Rollback de importaciones
- [ ] Notificaciones por email a docentes creados

---

## 📚 REFERENCIAS

- **PhpSpreadsheet**: https://phpspreadsheet.readthedocs.io/
- **Laravel File Upload**: https://laravel.com/docs/filesystem
- **Validation**: https://laravel.com/docs/validation

---

**Última actualización**: <?= date('Y-m-d H:i:s') ?>  
**Desarrollado por**: GitHub Copilot  
**Versión Laravel**: 12.34.0 | PHP 8.4.10
