# 📘 Guía de Uso - Gestión de Roles para Administrador

**Fecha:** 27 de Octubre, 2025  
**Usuario:** Super Administrador  
**Versión:** 1.0

---

## 🎯 ¿Qué puedes hacer como Administrador?

Como **super administrador**, tienes control total sobre los roles del sistema. Puedes:

✅ **Crear nuevos roles** (coordinador, secretaria, supervisor, etc.)  
✅ **Editar roles existentes** (cambiar permisos, descripción, nivel)  
✅ **Activar/Desactivar roles** (sin eliminarlos)  
✅ **Eliminar roles** (si no tienen usuarios asignados)  
✅ **Asignar permisos** a cada rol  
✅ **Ver qué usuarios tienen cada rol**  

---

## 📋 Flujo Completo: Crear un Rol de "Coordinador"

### Paso 1: Acceder al Panel de Roles

1. **Iniciar sesión** como administrador
2. En el menú de navegación, hacer clic en **"Roles"**
3. Verás la lista de roles actuales (admin, docente, etc.)

```
┌─────────────────────────────────────────────────────┐
│  GESTIÓN DE ROLES              [+ Nuevo Rol] ←──────┤ BOTÓN AQUÍ
├─────────────────────────────────────────────────────┤
│                                                     │
│  🔍 Buscar: [_______________] [Filtro] [Buscar]    │
│                                                     │
│  Tabla de Roles:                                   │
│  ┌─────────────────────────────────────────────┐   │
│  │ Nombre  │ Descripción │ Nivel │ Estado ... │   │
│  ├─────────────────────────────────────────────┤   │
│  │ admin   │ ...         │ 100   │ Activo     │   │
│  │ docente │ ...         │ 50    │ Activo     │   │
│  └─────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────┘
```

---

### Paso 2: Hacer Clic en "+ Nuevo Rol"

Al hacer clic en el botón azul **"+ Nuevo Rol"**, se abre el formulario de creación.

---

### Paso 3: Llenar el Formulario

```
┌──────────────────────────────────────────────────────────┐
│  CREAR NUEVO ROL                                         │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  Nombre del Rol * ────────────────┐                     │
│  [coordinador___________________] │ ← Solo minúsculas   │
│  Usa minúsculas sin espacios      │   sin espacios      │
│                                    │                     │
│  Nivel * ──────────────────────────┤                     │
│  [60___] ← 1-100                   │ ← Mayor nivel =     │
│  Mayor nivel = mayor jerarquía     │   más jerarquía     │
│                                                          │
│  Descripción ───────────────────────────────────────┐    │
│  [Coordinador académico de la facultad___________] │    │
│  [____________________________________________]     │    │
│                                                     │    │
│  Estado * ──────────────────────────────────────────┤    │
│  [v] Activo  [ ] Inactivo                          │    │
│                                                          │
│  PERMISOS ───────────────────────────────────────────    │
│                                                          │
│  📁 Usuarios                                             │
│     [✓] usuarios.ver      - Ver lista de usuarios       │
│     [✓] usuarios.crear    - Crear nuevos usuarios       │
│     [ ] usuarios.editar   - Editar usuarios             │
│     [ ] usuarios.eliminar - Eliminar usuarios           │
│                                                          │
│  📁 Docentes                                             │
│     [✓] docentes.ver      - Ver lista de docentes       │
│     [✓] docentes.crear    - Crear docentes              │
│     [✓] docentes.editar   - Editar docentes             │
│     [ ] docentes.eliminar - Eliminar docentes           │
│                                                          │
│  📁 Materias                                             │
│     [✓] materias.ver      - Ver materias                │
│     [✓] materias.crear    - Crear materias              │
│     [ ] materias.editar   - Editar materias             │
│     [ ] materias.eliminar - Eliminar materias           │
│                                                          │
│  📁 Grupos (Carga Horaria)                               │
│     [✓] grupos.ver        - Ver grupos                  │
│     [ ] grupos.crear      - Crear grupos                │
│     [ ] grupos.editar     - Editar grupos               │
│     [ ] grupos.eliminar   - Eliminar grupos             │
│                                                          │
│  📁 Horarios                                             │
│     [✓] horarios.ver      - Ver horarios                │
│     [ ] horarios.crear    - Crear horarios              │
│     [ ] horarios.editar   - Editar horarios             │
│     [ ] horarios.eliminar - Eliminar horarios           │
│                                                          │
│  📁 Asistencias                                          │
│     [✓] asistencias.ver   - Ver asistencias             │
│     [ ] asistencias.crear - Registrar asistencias       │
│     [ ] asistencias.eliminar - Eliminar asistencias     │
│                                                          │
│  📁 Reportes                                             │
│     [✓] reportes.ver      - Ver reportes                │
│     [✓] reportes.exportar - Exportar reportes           │
│                                                          │
│  📁 Sistema                                              │
│     [ ] sistema.configurar - Configurar sistema         │
│     [ ] sistema.logs       - Ver logs del sistema       │
│                                                          │
│  [Cancelar]              [💾 Crear Rol] ←────────────────┤
└──────────────────────────────────────────────────────────┘
```

---

### Paso 4: Ejemplo de Llenado para "Coordinador"

| Campo | Valor | Explicación |
|-------|-------|-------------|
| **Nombre** | `coordinador` | Todo en minúsculas, sin espacios |
| **Nivel** | `60` | Menor que admin (100), mayor que docente (50) |
| **Descripción** | `Coordinador académico de la facultad` | Descripción clara del rol |
| **Estado** | `Activo` | El rol está activo desde su creación |
| **Permisos** | Ver usuarios, docentes, materias, grupos, horarios, asistencias y reportes. Crear usuarios, docentes y materias. | Seleccionar según las necesidades |

---

### Paso 5: Hacer Clic en "💾 Crear Rol"

Al hacer clic:

1. ✅ **Validación automática**:
   - Verifica que el nombre no exista
   - Verifica formato (minúsculas, sin espacios)
   - Verifica nivel entre 1-100

2. ✅ **Creación del rol** en la base de datos

3. ✅ **Asignación de permisos** seleccionados

4. ✅ **Redirección** a la lista de roles con mensaje de éxito:

```
┌─────────────────────────────────────────────────────┐
│ ✅ ¡Rol creado exitosamente!                        │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│  GESTIÓN DE ROLES              [+ Nuevo Rol]        │
├─────────────────────────────────────────────────────┤
│  Tabla de Roles:                                    │
│  ┌──────────────────────────────────────────────┐   │
│  │ Nombre      │ Nivel │ Estado │ Permisos ... │   │
│  ├──────────────────────────────────────────────┤   │
│  │ admin       │ 100   │ Activo │ 29 permisos  │   │
│  │ coordinador │ 60    │ Activo │ 15 permisos  │ ← NUEVO
│  │ docente     │ 50    │ Activo │ 5 permisos   │   │
│  └──────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────┘
```

---

## 🔧 Otras Operaciones

### Editar un Rol

1. Click en **"✏️ Editar"** en la fila del rol
2. Modificar campos necesarios
3. Click en **"💾 Guardar Cambios"**

**Nota:** Los roles del sistema (`admin`, `docente`) tienen el nombre bloqueado (readonly).

---

### Activar/Desactivar un Rol

1. Click en botón **"Desactivar"** o **"Activar"**
2. El estado cambia inmediatamente
3. Los usuarios con rol inactivo mantienen el rol pero puede restringirse su acceso

**Nota:** No se puede desactivar el rol `admin`.

---

### Eliminar un Rol

1. Click en **"🗑️ Eliminar"** en la fila del rol
2. Confirmar en el popup

**Restricciones:**
- ❌ NO se puede eliminar `admin` ni `docente` (roles del sistema)
- ❌ NO se puede eliminar si tiene usuarios asignados
- ✅ Solo se pueden eliminar roles personalizados sin usuarios

---

## 📊 Niveles de Jerarquía Recomendados

| Rol | Nivel | Descripción |
|-----|-------|-------------|
| **admin** | 100 | Super administrador (máximo control) |
| **director** | 80 | Director de la facultad |
| **coordinador** | 60 | Coordinador académico |
| **docente** | 50 | Docente regular |
| **secretaria** | 40 | Personal administrativo |
| **auxiliar** | 30 | Asistente o auxiliar |
| **observador** | 10 | Solo lectura (reportes) |

**Regla:** Mayor nivel = mayor jerarquía y más permisos típicamente.

---

## 🔍 Búsqueda y Filtros

### Búsqueda por Nombre o Descripción

```
🔍 Buscar: [coordinador______] [Buscar]
```

Muestra solo los roles que contengan "coordinador" en el nombre o descripción.

---

### Filtrar por Estado

```
🔍 Buscar: [____________] [v Activo  ] [Buscar]
                         [ Inactivo ]
                         [ Todos    ]
```

Muestra solo roles activos, inactivos o todos.

---

## 🎓 Ejemplo Práctico: Crear 3 Roles Nuevos

### 1. Rol "Coordinador"

```
Nombre: coordinador
Nivel: 60
Descripción: Coordinador académico de la facultad
Estado: Activo
Permisos:
  ✓ Ver: usuarios, docentes, materias, grupos, horarios, asistencias, reportes
  ✓ Crear: usuarios, docentes, materias
  ✓ Editar: docentes, materias
  ✓ Exportar reportes
```

### 2. Rol "Secretaria"

```
Nombre: secretaria
Nivel: 40
Descripción: Personal administrativo
Estado: Activo
Permisos:
  ✓ Ver: usuarios, docentes, materias, horarios
  ✓ Crear: docentes (registro)
  ✓ Ver reportes
```

### 3. Rol "Observador"

```
Nombre: observador
Nivel: 10
Descripción: Solo puede consultar información
Estado: Activo
Permisos:
  ✓ Ver: horarios, asistencias, reportes
  (Sin permisos de crear, editar, eliminar)
```

---

## ⚠️ Validaciones y Restricciones

### Nombre del Rol

- ✅ **Permitido:** `coordinador`, `secretaria`, `supervisor_area`, `jefe-departamento`
- ❌ **NO Permitido:** `Coordinador`, `SECRETARIA`, `Supervisor Area`, `jefe.departamento`

**Regla:** Solo minúsculas, números, guiones (`-`) y guiones bajos (`_`). Sin espacios.

---

### Nivel

- ✅ **Permitido:** 1 a 100
- ❌ **NO Permitido:** 0, 101, 500, -10

---

### Descripción

- ✅ **Máximo:** 500 caracteres
- ✅ **Opcional:** Puede dejarse vacío

---

### Permisos

- ✅ Puedes seleccionar **cualquier combinación** de permisos
- ✅ Puedes **no seleccionar ninguno** (rol sin permisos)
- ✅ Puedes cambiar permisos después (editar rol)

---

## 🔐 Seguridad Implementada

### Protecciones Automáticas

1. **Solo el admin puede gestionar roles**
   - Middleware: `role:admin`
   - Los docentes y otros usuarios NO tienen acceso

2. **Roles del sistema protegidos**
   - `admin` y `docente` NO se pueden eliminar
   - `admin` NO se puede desactivar

3. **Validación de usuarios asignados**
   - NO se puede eliminar un rol si tiene usuarios
   - Mensaje: "No puedes eliminar este rol porque tiene X usuario(s) asignado(s)"

4. **Transacciones de base de datos**
   - Si algo falla al crear/editar, se revierte todo
   - Integridad de datos garantizada

---

## 📱 Uso en Dispositivos Móviles

El diseño es **responsive** (adaptable). Puedes gestionar roles desde:

- 💻 **Computadora de escritorio**
- 💼 **Laptop**
- 📱 **Tablet**
- 📱 **Celular**

El formulario se adapta automáticamente al tamaño de pantalla.

---

## ❓ Preguntas Frecuentes

### ¿Puedo crear un rol sin permisos?

Sí. El rol se creará pero no podrá hacer nada en el sistema hasta que le asignes permisos.

---

### ¿Puedo cambiar los permisos de un rol después de crearlo?

Sí. Click en **"✏️ Editar"**, modifica los checkboxes de permisos y guarda.

---

### ¿Qué pasa si elimino un permiso de un rol que ya tiene usuarios?

Los usuarios con ese rol **perderán inmediatamente** ese permiso. Se aplica en tiempo real.

---

### ¿Puedo tener varios usuarios con el mismo rol?

Sí. Un rol puede tener **0, 1 o muchos usuarios** asignados.

---

### ¿Puedo asignar varios roles a un mismo usuario?

Sí. Cuando editas un usuario, puedes seleccionar uno o más roles en el formulario.

---

### ¿Qué pasa si desactivo un rol?

El rol sigue existiendo y los usuarios lo mantienen, pero puedes usar el estado para:
- Prevenir la asignación de nuevos usuarios
- Implementar lógica adicional de restricción (requiere desarrollo extra)

---

### ¿Puedo cambiar el nombre de "admin" o "docente"?

Técnicamente sí (el campo es readonly en frontend pero se puede editar), pero **NO es recomendable** porque:
- El código puede tener referencias hardcoded a estos nombres
- El middleware `role:admin` busca específicamente el rol "admin"

---

## 🚀 Siguiente Paso: Asignar Roles a Usuarios

Una vez creado el rol **"coordinador"**, puedes:

1. Ir a **"Gestión de Usuarios"**
2. Click en **"+ Nuevo Usuario"** o **"✏️ Editar"** un usuario existente
3. En el formulario, seleccionar el rol **"coordinador"** en el campo "Roles"
4. Guardar

El usuario ahora tendrá todos los permisos del rol coordinador.

---

## 📋 Checklist: Crear un Nuevo Rol

- [ ] 1. Iniciar sesión como admin
- [ ] 2. Ir a menú **"Roles"**
- [ ] 3. Click en **"+ Nuevo Rol"**
- [ ] 4. Escribir nombre en **minúsculas** sin espacios
- [ ] 5. Asignar nivel jerárquico (1-100)
- [ ] 6. Escribir descripción clara
- [ ] 7. Seleccionar estado (Activo recomendado)
- [ ] 8. Marcar checkboxes de permisos necesarios
- [ ] 9. Click en **"💾 Crear Rol"**
- [ ] 10. Verificar mensaje de éxito ✅
- [ ] 11. Ver el nuevo rol en la tabla
- [ ] 12. *(Opcional)* Asignar el rol a usuarios

---

## 🎉 Resumen

El sistema de gestión de roles está **100% funcional** y listo para usar. Como administrador puedes:

✅ **Crear roles personalizados** (coordinador, secretaria, etc.)  
✅ **Asignar permisos granulares** por módulo  
✅ **Gestionar la jerarquía** con niveles  
✅ **Controlar el acceso** activando/desactivando roles  
✅ **Ver en tiempo real** cuántos usuarios tienen cada rol  

**Todo desde el panel de administración**, sin necesidad de tocar código o base de datos.

---

**Desarrollado por:** GitHub Copilot  
**Fecha:** 27 de Octubre, 2025  
**Versión del Sistema:** Laravel 11.x
