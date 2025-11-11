# Validación de Choques de Horarios en Importación

## 📋 Descripción General

Se ha implementado un sistema robusto de validación de conflictos de horarios durante el proceso de importación desde Excel. Este sistema detecta 4 tipos de choques antes de crear los registros en la base de datos.

---

## 🔍 Tipos de Validaciones

### 1. **Choque de Aula**
**Problema:** Misma aula ocupada por diferentes grupos en el mismo horario.

**Validación:**
- ✅ Verifica que el aula esté disponible en el día y horario especificado
- ✅ Compara con horarios existentes del mismo semestre
- ✅ Detecta superposiciones parciales de horarios

**Ejemplo de conflicto:**
```
❌ MAT101 - Grupo A: Lunes 08:00-10:00 en Aula 101
❌ FIS100 - Grupo B: Lunes 08:00-10:00 en Aula 101
```

**Mensaje de error:**
```
❌ CHOQUE DE AULA: Lunes 08:00-10:00 - Aula 101 ya ocupada por MAT101 - Grupo A
```

---

### 2. **Choque de Grupo**
**Problema:** Mismo grupo programado en dos lugares diferentes al mismo tiempo.

**Validación:**
- ✅ Verifica que el grupo no tenga otro horario simultáneo
- ✅ Detecta si el grupo ya existe con horarios en ese momento
- ✅ Previene doble asignación del mismo grupo

**Ejemplo de conflicto:**
```
❌ QUI150 - Grupo D: Martes 14:00-16:00 en Aula 301
❌ QUI150 - Grupo D: Martes 14:00-16:00 en Aula 302
```

**Mensaje de error:**
```
❌ CHOQUE DE GRUPO: Martes 14:00-16:00 - El grupo D ya tiene clase en Aula 301
```

---

### 3. **Choque de Docente**
**Problema:** Mismo docente asignado a dos clases diferentes en el mismo horario.

**Validación:**
- ✅ Verifica que el docente no tenga otra clase al mismo tiempo
- ✅ Compara con todos los grupos del semestre activo
- ✅ Detecta superposiciones parciales

**Ejemplo de conflicto:**
```
❌ MAT101 - Grupo A: Lunes 08:00-10:00 - Docente: PEREZ GOMEZ JUAN
❌ FIS100 - Grupo C: Lunes 08:00-10:00 - Docente: PEREZ GOMEZ JUAN
```

**Mensaje de error:**
```
❌ CHOQUE DE DOCENTE: Lunes 08:00-10:00 - PEREZ GOMEZ JUAN ya tiene clase con MAT101 - Grupo A en Aula 101
```

---

### 4. **Choque Interno**
**Problema:** Conflictos dentro de la misma fila del Excel (múltiples horarios del mismo grupo).

**Validación:**
- ✅ Compara todos los horarios de la misma fila entre sí
- ✅ Detecta si el grupo está en dos lugares al mismo tiempo
- ✅ Detecta si la misma aula se asigna dos veces en el mismo horario

**Ejemplo de conflicto:**
```
Fila del Excel:
SIGLA | SEMESTRE | GRUPO | ... | DIA | HORA | AULA | DIA | HORA | AULA
QUI150| 2        | D     | ... | Mar | 14:00-16:00 | 301 | Mar | 14:00-16:00 | 302
                                  ^^^^^^^^^^^^^^^^^^^     ^^^^^^^^^^^^^^^^^^^
                                  CONFLICTO INTERNO
```

**Mensaje de error:**
```
❌ CHOQUE INTERNO: Mar - El grupo tiene dos horarios simultáneos (14:00-16:00 y 14:00-16:00)
❌ CHOQUE INTERNO AULA: Mar - Aula 301 asignada dos veces (14:00-16:00 y 14:00-16:00)
```

---

## 🛠️ Implementación Técnica

### Flujo de Validación

```
┌─────────────────────────────────────┐
│ 1. Leer fila del Excel              │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ 2. Extraer todos los horarios       │
│    y guardarlos temporalmente       │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ 3. VALIDAR cada horario:            │
│    ├─ Choque de Aula                │
│    ├─ Choque de Grupo               │
│    ├─ Choque de Docente             │
│    └─ Choque Interno                │
└──────────────┬──────────────────────┘
               │
               ├─ ❌ HAY ERRORES
               │   └─> No crear horarios
               │       Mostrar conflictos
               │
               └─ ✅ SIN ERRORES
                   └─> Crear todos los horarios
```

### Métodos Principales

#### 1. `verificarChoqueAula($aulaId, $dia, $horaInicio, $horaFin, $grupoActualId, $semestreId)`
Verifica disponibilidad del aula consultando la base de datos.

**Lógica:**
```php
- Busca horarios existentes en la misma aula
- Mismo día de la semana
- Mismo semestre activo
- Detecta superposición de rangos horarios
- Retorna null si está libre, o datos del conflicto
```

#### 2. `verificarChoqueGrupo($grupoId, $dia, $horaInicio, $horaFin)`
Verifica que el grupo no tenga otro horario simultáneo.

**Lógica:**
```php
- Busca horarios del grupo en ese día
- Detecta superposición de horas
- Retorna null si está libre, o datos del conflicto
```

#### 3. `verificarChoqueDocente($docenteId, $dia, $horaInicio, $horaFin, $grupoActualId, $semestreId)`
Verifica que el docente no tenga otra clase al mismo tiempo.

**Lógica:**
```php
- Busca grupos del docente en el semestre activo
- Busca horarios de esos grupos en ese día
- Detecta superposición de horas
- Retorna null si está libre, o datos del conflicto
```

#### 4. `horariosSeSuperponen($inicio1, $fin1, $inicio2, $fin2)`
Algoritmo para detectar superposición de rangos horarios.

**Lógica:**
```php
// Se superponen si:
// - El horario 2 empieza antes de que termine el horario 1 Y
// - El horario 2 termina después de que empiece el horario 1

Ejemplos:
✅ 08:00-10:00 y 10:00-12:00 → NO se superponen (consecutivos)
❌ 08:00-10:00 y 09:00-11:00 → SÍ se superponen (1 hora)
❌ 08:00-12:00 y 09:00-10:00 → SÍ se superponen (envuelve)
✅ 08:00-10:00 y 14:00-16:00 → NO se superponen (separados)
```

---

## 📊 Proceso de Importación

### Paso 1: Recopilación
```php
$horariosPendientes = [];
foreach ($columnas as $horario) {
    // Validar formato
    // Crear/buscar aula
    // Agregar a array pendiente
}
```

### Paso 2: Validación Completa
```php
$tieneErrores = false;
foreach ($horariosPendientes as $horario) {
    // Validar choque de aula
    // Validar choque de grupo
    // Validar choque de docente
    // Validar choques internos
    if ($conflicto) {
        $tieneErrores = true;
        $errores[] = $mensaje;
    }
}
```

### Paso 3: Creación o Rechazo
```php
if ($tieneErrores) {
    // NO crear ningún horario
    // Retornar lista de conflictos
} else {
    // Crear TODOS los horarios
    // Retornar éxito
}
```

---

## 🎨 Interfaz de Usuario

### Reporte de Importación

**Vista mejorada con:**
- 🟢 Badge verde para casos exitosos
- 🔴 Badge rojo para casos con conflictos
- 📋 Lista detallada de errores de validación
- 💡 Advertencias informativas (docentes creados, aulas nuevas, etc.)

**Estructura del reporte:**
```
┌──────────────────────────────────────────────────────┐
│ Línea │ Estado │ Mensaje │ Detalles                  │
├──────────────────────────────────────────────────────┤
│   2   │   ✅   │ MAT101-A: 2 horarios creados         │
│       │        │ ✓ Materia creada: MAT101             │
│       │        │ ✓ Docente creado: PEREZ GOMEZ (100)  │
├──────────────────────────────────────────────────────┤
│   3   │   ❌   │ No se crearon horarios               │
│       │        │ ❌ CHOQUE DE AULA: Lunes 08:00-10:00 │
│       │        │    Aula 101 ocupada por MAT101-A     │
│       │        │ 🔴 1 conflicto(s)                    │
└──────────────────────────────────────────────────────┘
```

---

## 🧪 Casos de Prueba

### Generar Excel de Prueba

```bash
php scripts/generar-excel-prueba-choques.php
```

Este script genera un archivo Excel con 10 casos:
- ✅ 5 casos válidos (deben pasar)
- ❌ 4 casos con conflictos (deben fallar)
- 🔄 1 caso de actualización (reemplaza horarios previos)

### Casos Incluidos:

| Caso | Tipo | Resultado Esperado |
|------|------|--------------------|
| 1 | Válido | ✅ Pasa - Sin conflictos |
| 2 | Choque de Aula | ❌ Falla - Aula 101 ocupada |
| 3 | Choque de Docente | ❌ Falla - Docente ocupado |
| 4 | Choque Interno | ❌ Falla - Grupo en dos lugares |
| 5 | Válido | ✅ Pasa - Horarios separados |
| 6 | Superposición Parcial | ❌ Falla - Se solapan 1 hora |
| 7 | Válido múltiple | ✅ Pasa - 3 sesiones sin conflicto |
| 8 | Actualización | ✅ Pasa - Reemplaza horarios |
| 9 | Diferentes Aulas | ✅ Pasa - Mismo horario, diferentes aulas |
| 10 | Diferentes Aulas | ✅ Pasa - Validación final |

---

## 📝 Mensajes de Error

### Formato de Mensajes

```
❌ [TIPO DE CONFLICTO]: [Día] [Hora] - [Descripción detallada]
```

### Ejemplos:

1. **Choque de Aula:**
   ```
   ❌ CHOQUE DE AULA: Martes 18:15-20:30 - Aula 14 ya ocupada por MAT101 - F1
   ```

2. **Choque de Docente:**
   ```
   ❌ CHOQUE DE DOCENTE: Jueves 9:15-11:30 - AVENDAÑO GONZALES EUDAL ya tiene clase con FIS100 - A en Aula 12
   ```

3. **Choque de Grupo:**
   ```
   ❌ CHOQUE DE GRUPO: Viernes 9:15-11:30 - El grupo F1 ya tiene clase en Aula 14
   ```

4. **Choque Interno:**
   ```
   ❌ CHOQUE INTERNO: Jueves - El grupo tiene dos horarios simultáneos (18:15-20:30 y 18:15-20:30)
   ❌ CHOQUE INTERNO AULA: Jueves - Aula 12 asignada dos veces (18:15-20:30 y 18:15-20:30)
   ```

---

## ✅ Beneficios

### Para Administradores:
- ✨ **Prevención automática** de errores de asignación
- 🚀 **Importación masiva** sin preocupaciones
- 📊 **Reportes detallados** de conflictos
- 🔍 **Detección temprana** antes de guardar en BD

### Para el Sistema:
- 🛡️ **Integridad de datos** garantizada
- ⚡ **Validaciones en memoria** (rápidas)
- 🔄 **Transacciones atómicas** (todo o nada)
- 📝 **Trazabilidad completa** de errores

### Para Docentes y Estudiantes:
- ✅ **Horarios consistentes** sin solapamientos
- 🎯 **Aulas garantizadas** sin doble reserva
- 📅 **Calendario confiable** sin conflictos

---

## 🔧 Mantenimiento

### Agregar Nuevas Validaciones

Para agregar un nuevo tipo de validación:

1. **Crear método de validación:**
```php
private function verificarNuevoTipo($parametros)
{
    // Lógica de validación
    // Retornar null si pasa, o array con detalles si falla
}
```

2. **Integrar en el flujo:**
```php
// En procesarFila(), después de línea 180
$nuevoChoque = $this->verificarNuevoTipo(...);
if ($nuevoChoque) {
    $tieneErrores = true;
    $resultado['errores_validacion'][] = "❌ NUEVO TIPO: {$detalles}";
}
```

3. **Actualizar documentación y tests**

---

## 📚 Referencias

### Archivos Relacionados:
- `app/Http/Controllers/HorarioImportController.php` - Controlador principal
- `resources/views/horarios/import-result.blade.php` - Vista de resultados
- `scripts/generar-excel-prueba-choques.php` - Generador de tests
- `docs/VALIDACION_CHOQUES_HORARIOS.md` - Esta documentación

### Base de Datos:
- Tabla: `horarios` - Almacena los horarios
- Tabla: `grupos` - Relaciona materia-docente-semestre
- Tabla: `aulas` - Catálogo de aulas
- Tabla: `semestres` - Períodos académicos

---

## 🎓 Conclusión

El sistema de validación de choques de horarios garantiza:
1. ✅ **No hay conflictos de aulas** - Una aula solo puede estar ocupada por un grupo a la vez
2. ✅ **No hay conflictos de docentes** - Un docente solo puede dar una clase a la vez
3. ✅ **No hay conflictos de grupos** - Un grupo solo puede estar en un lugar a la vez
4. ✅ **Consistencia interna** - Los datos del Excel son validados antes de guardar

**Resultado:** Sistema de horarios robusto, confiable y sin errores de asignación.
