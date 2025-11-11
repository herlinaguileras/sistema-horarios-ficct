# Eliminación de Docentes - Guía Completa

## 🔍 Problema Identificado

Al intentar eliminar un docente que tiene grupos asignados, se genera el siguiente error:

```
SQLSTATE[23503]: Foreign key violation: 7 ERROR: update o delete en «docentes» 
viola la llave foránea «grupos_docente_id_foreign» en la tabla «grupos»
```

### Causa del Error

La tabla `grupos` tiene una llave foránea (`docente_id`) que referencia a la tabla `docentes`. PostgreSQL **previene la eliminación** de un docente si todavía hay grupos asignados a él para mantener la integridad referencial.

---

## ✅ Solución Implementada

### 1. **Validación Previa**

El sistema ahora **verifica primero** si el docente tiene grupos asignados antes de intentar eliminarlo.

**Código en `DocenteController::destroy()`:**

```php
// Verificar si el docente tiene grupos asignados
$gruposCount = $docente->grupos()->count();

if ($gruposCount > 0) {
    return redirect()->route('docentes.index')
        ->with('error', "❌ No se puede eliminar el docente porque tiene {$gruposCount} grupo(s) asignado(s). Por favor, reasigna o elimina los grupos primero.");
}
```

### 2. **Mensaje de Error Claro**

Si el docente tiene grupos, el usuario verá:

```
❌ No se puede eliminar el docente porque tiene 2 grupo(s) asignado(s). 
   Por favor, reasigna o elimina los grupos primero.
```

### 3. **Advertencia en la Interfaz**

El botón de eliminar ahora muestra una advertencia personalizada:

- **Docente SIN grupos:** Mensaje estándar de confirmación
- **Docente CON grupos:** Advertencia sobre la cantidad de grupos asignados

---

## 📋 Proceso de Eliminación Segura

### Caso A: Docente SIN Grupos Asignados

**Flujo normal:**

```
1. Clic en "Eliminar" → Confirmación
2. Sistema elimina:
   ├─ Títulos del docente
   ├─ Relación con rol "docente"
   ├─ Registro en tabla "docentes"
   └─ Usuario asociado
3. ✅ Eliminación exitosa
```

### Caso B: Docente CON Grupos Asignados

**Flujo bloqueado:**

```
1. Clic en "Eliminar" → Advertencia especial
2. Usuario confirma
3. Sistema verifica grupos → HAY 2 GRUPOS
4. ❌ Eliminación bloqueada
5. Mensaje: "Reasigna o elimina los grupos primero"
```

**Opciones disponibles:**

#### Opción 1: Reasignar Grupos a Otro Docente

1. Ir a **Gestión de Grupos** (`/grupos`)
2. Editar cada grupo del docente
3. Asignar un nuevo docente
4. Volver a **Gestión de Docentes**
5. Eliminar el docente (ahora sin grupos)

#### Opción 2: Eliminar los Grupos

1. Ir a **Gestión de Grupos** (`/grupos`)
2. Eliminar los grupos del docente
3. Volver a **Gestión de Docentes**
4. Eliminar el docente (ahora sin grupos)

---

## 🛠️ Script de Verificación

### Ejecutar Verificación Manual

```bash
php scripts/verificar-grupos-docentes.php
```

### Información Proporcionada

El script muestra:

1. **Lista de docentes con grupos:**
   - Nombre del docente
   - Cantidad de grupos
   - Detalles de cada grupo (materia, semestre)

2. **Lista de docentes sin grupos:**
   - Docentes que se pueden eliminar de forma segura

3. **Recomendaciones específicas:**
   - Grupos en semestre activo → REASIGNAR
   - Grupos en semestres pasados → ELIMINAR

### Ejemplo de Salida:

```
📊 ANÁLISIS DE DOCENTES Y GRUPOS:
─────────────────────────────────────────────────────────────

👨‍🏫 AVENDAÑO GONZALES EUDAL (Código: 100)
   📚 1 grupo(s) asignado(s):
   • CALCULO I - Grupo F1 (Semestre: Gestion 2 - 2025)

─────────────────────────────────────────────────────────────

📈 RESUMEN ESTADÍSTICO:
─────────────────────────────────────────────────────────────
Total de docentes: 5
├─ Con grupos asignados: 2
├─ Sin grupos asignados: 3
└─ Total de grupos: 2

✅ DOCENTES QUE SE PUEDEN ELIMINAR DIRECTAMENTE:
─────────────────────────────────────────────────────────────
• LOPEZ SANTOS ANA (Código: 102)
• GONZALES ARREDONDO (Código: 103)
• GONZALES RODRIGO (Código: 104)

⚠️  DOCENTES QUE REQUIEREN ACCIÓN PREVIA:
─────────────────────────────────────────────────────────────
❌ AVENDAÑO GONZALES EUDAL (ID: 33)
   Grupos asignados: 1
   Acción requerida: Reasignar o eliminar grupos primero
```

---

## 🎯 Mejores Prácticas

### ✅ Recomendaciones:

1. **Antes de finalizar un semestre:**
   - Revisar docentes que ya no trabajarán
   - Reasignar sus grupos para el nuevo semestre
   - Eliminar docentes sin grupos

2. **Durante un semestre activo:**
   - NO eliminar docentes con grupos activos
   - Solo reasignar si hay cambio de docente
   - Mantener historial de semestres pasados

3. **Uso del script de verificación:**
   - Ejecutar antes de operaciones masivas
   - Identificar docentes "huérfanos" (sin grupos)
   - Planificar reasignaciones

### ❌ Evitar:

1. **NO** eliminar docentes durante semestre activo si tienen grupos
2. **NO** forzar eliminaciones modificando la base de datos
3. **NO** intentar eliminar sin verificar dependencias

---

## 🔐 Integridad de Datos

### Foreign Keys Implementadas:

```sql
grupos.docente_id → REFERENCES docentes(id)
```

### Comportamiento:

- **ON DELETE:** No especificado (restrictivo por defecto)
- **Acción:** PREVIENE eliminación si hay registros dependientes
- **Beneficio:** Garantiza integridad referencial

### Alternativas Evaluadas (NO Implementadas):

#### Opción A: CASCADE
```sql
ON DELETE CASCADE
```
**Problema:** Eliminaría automáticamente todos los grupos del docente (pérdida de datos)

#### Opción B: SET NULL
```sql
ON DELETE SET NULL
```
**Problema:** Dejaría grupos sin docente asignado (inconsistencia)

### Solución Adoptada:

**RESTRICT (actual)** + **Validación en Aplicación**
- ✅ Previene pérdida accidental de datos
- ✅ Obliga a tomar decisión explícita
- ✅ Mantiene integridad de registros

---

## 📊 Diagrama de Flujo

```
┌─────────────────────────────┐
│ Usuario: Eliminar Docente   │
└──────────────┬──────────────┘
               │
               ▼
┌─────────────────────────────┐
│ Verificar si tiene grupos   │
└──────────┬──────────┬───────┘
           │          │
    SIN GRUPOS    CON GRUPOS
           │          │
           ▼          ▼
    ┌──────────┐  ┌────────────────────────┐
    │ ELIMINAR │  │ BLOQUEAR ELIMINACIÓN   │
    └────┬─────┘  └───────┬────────────────┘
         │                 │
         ▼                 ▼
    ┌──────────┐  ┌────────────────────────┐
    │ Títulos  │  │ Mostrar mensaje error: │
    │ Roles    │  │ "Reasigna o elimina    │
    │ Docente  │  │  grupos primero"       │
    │ Usuario  │  └────────────────────────┘
    └────┬─────┘
         │
         ▼
    ┌──────────┐
    │ ✅ ÉXITO │
    └──────────┘
```

---

## 🧪 Casos de Prueba

### Test 1: Eliminar Docente SIN Grupos

**Entrada:**
- Docente: LOPEZ SANTOS ANA (ID: 102)
- Grupos: 0

**Resultado Esperado:**
```
✅ ¡Docente eliminado exitosamente!
```

**Verificación:**
- Usuario eliminado de `users`
- Registro eliminado de `docentes`
- Roles desvinculados de `role_user`

### Test 2: Intentar Eliminar Docente CON Grupos

**Entrada:**
- Docente: AVENDAÑO GONZALES EUDAL (ID: 33)
- Grupos: 1 (CALCULO I - F1)

**Resultado Esperado:**
```
❌ No se puede eliminar el docente porque tiene 1 grupo(s) asignado(s). 
   Por favor, reasigna o elimina los grupos primero.
```

**Verificación:**
- Docente NO eliminado
- Grupos NO afectados
- Mensaje de error visible

### Test 3: Reasignar Grupo y Luego Eliminar

**Pasos:**
1. Reasignar grupo de Docente A a Docente B
2. Eliminar Docente A

**Resultado Esperado:**
```
✅ ¡Docente eliminado exitosamente!
```

**Verificación:**
- Docente A eliminado
- Grupo ahora asignado a Docente B
- Sin errores de foreign key

---

## 📝 Archivos Modificados

### 1. `app/Http/Controllers/DocenteController.php`

**Cambios:**
- Agregada validación de grupos en `destroy()`
- Mensaje de error descriptivo
- Orden de eliminación seguro

### 2. `resources/views/docentes/index.blade.php`

**Cambios:**
- Mensaje de error en rojo para advertencias
- Confirmación personalizada según grupos
- Display de mensajes flash de error

### 3. `scripts/verificar-grupos-docentes.php` (NUEVO)

**Funcionalidad:**
- Análisis completo de docentes y grupos
- Recomendaciones específicas
- Identificación de docentes eliminables

---

## 🎓 Conclusión

### Beneficios de la Solución:

1. ✅ **Previene errores** de foreign key
2. ✅ **Guía al usuario** con mensajes claros
3. ✅ **Protege datos** importantes (grupos, horarios)
4. ✅ **Facilita planificación** con script de verificación
5. ✅ **Mantiene integridad** de la base de datos

### Estado Final:

```
╔═══════════════════════════════════════════════════════════╗
║  ✅ ELIMINACIÓN DE DOCENTES FUNCIONANDO CORRECTAMENTE    ║
║                                                           ║
║  📊 Validación previa de dependencias                    ║
║  🛡️ Protección contra pérdida de datos                   ║
║  📝 Mensajes de error descriptivos                       ║
║  🔍 Script de verificación disponible                    ║
║  ✨ Flujo de trabajo optimizado                          ║
║                                                           ║
║  🎉 Sistema robusto y seguro                             ║
╚═══════════════════════════════════════════════════════════╝
```

**El sistema ahora maneja correctamente la eliminación de docentes, protegiendo la integridad de los datos y guiando al usuario en cada paso.** 🚀
