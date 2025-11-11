# Sistema de Módulos - Guía Simplificada

## 🎯 Concepto Principal

El sistema funciona con **módulos predefinidos**. El administrador simplemente **asigna módulos** a cada rol, y el usuario automáticamente tiene acceso **completo** a ese módulo.

## 📦 Módulos Disponibles

El sistema tiene **9 módulos predefinidos**:

| Módulo | Descripción | Permisos Incluidos |
|--------|-------------|-------------------|
| **usuarios** | Gestión de usuarios del sistema | Ver, crear, editar, eliminar usuarios |
| **roles** | Gestión de roles | Ver, crear, editar, eliminar roles |
| **docentes** | Gestión de profesores | Ver, crear, editar, eliminar docentes |
| **materias** | Gestión de asignaturas | Ver, crear, editar, eliminar materias |
| **aulas** | Gestión de salones y espacios | Ver, crear, editar, eliminar aulas |
| **grupos** | Gestión de grupos de estudiantes | Ver, crear, editar, eliminar grupos |
| **semestres** | Gestión de períodos académicos | Ver, crear, editar, eliminar semestres |
| **horarios** | Gestión de horarios y asistencias | Ver, crear, editar, eliminar horarios + marcar asistencias |
| **estadisticas** | Ver estadísticas y reportes | Ver estadísticas, generar reportes |

## 🔧 Cómo Funciona

### 1. Crear un Rol
```
Admin va a: Roles > Crear Rol
- Nombre: coordinador_academico
- Descripción: Coordinador de actividades académicas
- Módulos: [✓] Horarios  [✓] Estadísticas
```

### 2. Sistema Asigna Acceso Automáticamente
Al seleccionar "Horarios", el rol obtiene **automáticamente**:
- ✅ Ver lista de horarios
- ✅ Crear nuevos horarios
- ✅ Editar horarios existentes
- ✅ Eliminar horarios
- ✅ Marcar asistencias (desde horarios)
- ✅ Generar QR para asistencias

### 3. Usuario ve su Dashboard
```
Dashboard del Coordinador:
┌──────────────┐  ┌──────────────┐
│  📅 Horarios │  │ 📊 Estadís. │
│  y Asisten.  │  │             │
└──────────────┘  └──────────────┘
```

### 4. Navegación Dinámica
El menú superior **solo muestra** los módulos asignados:
- Admin → Ve todos los módulos
- Coordinador → Solo ve Horarios y Estadísticas
- Usuario sin módulos → Solo ve Dashboard

## 🛡️ Seguridad

### Middleware `module`
Todas las rutas están protegidas:
```php
// Rutas de horarios (requiere módulo 'horarios')
Route::middleware(['module:horarios'])->group(function() {
    Route::resource('horarios', HorarioController::class);
});
```

### Verificación en el Backend
```php
// En cualquier controlador
if (!auth()->user()->hasModule('horarios')) {
    abort(403, 'No tienes acceso a este módulo.');
}
```

### Verificación en Vistas
```blade
@if(Auth::user()->hasModule('horarios'))
    <a href="{{ route('horarios.index') }}">Ver Horarios</a>
@endif
```

## 📝 Crear un Nuevo Rol

### Paso 1: Admin accede a Roles
```
Navegación > Roles > Crear Rol
```

### Paso 2: Llenar Formulario
```
Nombre: secretaria
Descripción: Secretaria administrativa
```

### Paso 3: Seleccionar Módulos
```
☐ Usuarios
☐ Roles
☑ Docentes       ← Marcar los módulos necesarios
☑ Materias       ← Marcar los módulos necesarios
☑ Aulas          ← Marcar los módulos necesarios
☐ Grupos
☐ Semestres
☐ Horarios
☐ Estadísticas
```

### Paso 4: Guardar
El sistema automáticamente:
1. Crea el rol
2. Asocia los módulos seleccionados
3. Aplica las reglas de acceso

## 🔄 Editar Módulos de un Rol

1. Ir a **Roles > Editar [Rol]**
2. Cambiar módulos seleccionados
3. Guardar
4. **Los cambios aplican inmediatamente** a todos los usuarios con ese rol

## ⚠️ Reglas Importantes

### 1. Admin Siempre Tiene Acceso Total
```php
if ($user->hasRole('admin')) {
    return true; // Admin bypassa todo
}
```

### 2. Módulo = Acceso Completo
No hay permisos granulares dentro del módulo. Si tiene el módulo "docentes", puede hacer **todo** en docentes.

### 3. Sin Módulo = Sin Acceso
Si intentas acceder a una página sin tener el módulo:
```
Error 403: No tienes acceso a este módulo.
```

## 🎨 Dashboard Personalizado

### Usuarios con Roles Personalizados
Ven un dashboard con tarjetas de sus módulos:
```
╔════════════════════════════════════╗
║   Panel de Control - Coordinador   ║
╠════════════════════════════════════╣
║                                    ║
║  ┌─────────────┐  ┌─────────────┐ ║
║  │ 📅 Horarios │  │📊 Estadíst. │ ║
║  │ y Asist.    │  │             │ ║
║  │ Click aquí→ │  │ Click aquí→ │ ║
║  └─────────────┘  └─────────────┘ ║
║                                    ║
╚════════════════════════════════════╝
```

### Roles Especiales (docente, admin)
Tienen dashboards personalizados específicos para su función.

## 🗂️ Archivos Clave

### Base de Datos
- `role_modules` → Tabla que relaciona roles con módulos

### Modelos
- `Role.php` → Método `hasModule($moduleName)`
- `User.php` → Método `hasModule($moduleName)`
- `RoleModule.php` → Lista de módulos disponibles

### Middleware
- `CheckModule.php` → Verifica acceso a módulos

### Rutas
- `web.php` → Todas las rutas protegidas con `middleware(['module:xxx'])`

### Vistas
- `dashboards/custom-role.blade.php` → Dashboard para roles personalizados
- `roles/create.blade.php` → Formulario para asignar módulos
- `layouts/navigation.blade.php` → Menú dinámico según módulos

## 📊 Base de Datos

### Tabla: `role_modules`
```sql
id | role_id | module_name  | created_at | updated_at
---|---------|--------------|------------|------------
1  | 6       | horarios     | ...        | ...
2  | 6       | estadisticas | ...        | ...
```

### Consultas Útiles
```sql
-- Ver módulos de un rol
SELECT module_name 
FROM role_modules 
WHERE role_id = 6;

-- Ver todos los roles con sus módulos
SELECT r.name, GROUP_CONCAT(rm.module_name) as modulos
FROM roles r
LEFT JOIN role_modules rm ON r.id = rm.role_id
GROUP BY r.id;
```

## ✅ Ventajas de Este Sistema

1. **Simple**: Admin solo marca checkboxes
2. **Rápido**: No configurar 50 permisos individuales
3. **Seguro**: Middleware protege todas las rutas
4. **Flexible**: Cambiar módulos de un rol en segundos
5. **Escalable**: Agregar nuevos módulos es fácil
6. **Intuitivo**: Dashboard muestra visualmente los módulos

## 🚀 Ejemplo Práctico

### Escenario
Contratar un nuevo coordinador de asistencias.

### Proceso (2 minutos)
1. **Crear usuario**: coordinador@ficct.edu.bo
2. **Crear/usar rol**: "coordinador" con módulos [horarios, estadisticas]
3. **Asignar rol** al usuario
4. ✅ **Listo** - El coordinador ya puede trabajar

### Lo que puede hacer
- ✅ Ver todos los horarios
- ✅ Marcar asistencias con QR
- ✅ Ver estadísticas de asistencia
- ❌ No puede gestionar usuarios
- ❌ No puede crear roles
- ❌ No puede modificar docentes

---

**Última actualización**: Noviembre 2025  
**Sistema**: Laravel 12 + PostgreSQL
