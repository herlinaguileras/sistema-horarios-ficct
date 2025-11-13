# 🔍 DIAGNÓSTICO: Sistema de Módulos y Paquetes de Navegación

**Fecha:** 13 de Noviembre 2025  
**Problema Reportado:** Al asignar un rol con ciertos módulos, los paquetes no se muestran correctamente en la navegación

---

## 📋 1. DIAGNÓSTICO DEL PROBLEMA

### ✅ Sistema Actual Funcionando Correctamente

**Base de Datos:**
- Tabla `roles`: Almacena roles (admin, docente, custom roles)
- Tabla `role_modules`: Relaciona roles con módulos individuales
- Relación correcta: Un rol puede tener múltiples módulos

**Modelo User:**
- Método `hasModule($moduleName)`: Verifica si el usuario tiene acceso a un módulo específico
- Método `hasRole($roleName)`: Verifica si el usuario tiene un rol
- Admin tiene acceso a TODOS los módulos automáticamente

**Módulos Disponibles en RoleModule::availableModules():**
1. `usuarios` - Gestión de usuarios
2. `roles` - Gestión de roles y permisos
3. `docentes` - Gestión de profesores
4. `materias` - Gestión de asignaturas
5. `aulas` - Gestión de salones
6. `grupos` - Gestión de grupos
7. `semestres` - Gestión de períodos académicos
8. `horarios` - Gestión de horarios y asistencias
9. `importacion` - Importación masiva de horarios
10. `estadisticas` - Ver estadísticas y reportes
11. `bitacora` - Bitácora del sistema (auditoría)

---

### ❌ PROBLEMA IDENTIFICADO

**En navigation.blade.php se usan 3 PAQUETES:**

#### Paquete 1: "Usuarios y Roles"
```blade
@if(Auth::user()->hasModule('usuarios') || Auth::user()->hasModule('roles'))
    <x-nav-dropdown title="Usuarios y Roles">
        @if(Auth::user()->hasModule('usuarios'))
            <x-dropdown-item>Usuarios</x-dropdown-item>
        @endif
        @if(Auth::user()->hasModule('roles'))
            <x-dropdown-item>Roles</x-dropdown-item>
        @endif
    </x-nav-dropdown>
@endif
```
**✅ Módulos:** `usuarios`, `roles`

#### Paquete 2: "Periodo Académico"
```blade
@if(Auth::user()->hasModule('docentes') || Auth::user()->hasModule('materias') || 
    Auth::user()->hasModule('aulas') || Auth::user()->hasModule('grupos') || 
    Auth::user()->hasModule('semestres') || Auth::user()->hasModule('horarios'))
    <x-nav-dropdown title="Periodo Académico">
        <!-- 6 módulos aquí -->
    </x-nav-dropdown>
@endif
```
**✅ Módulos:** `docentes`, `materias`, `aulas`, `grupos`, `semestres`, `horarios`

#### Paquete 3: "Reportes"
```blade
@if(Auth::user()->hasModule('estadisticas') || Auth::user()->hasModule('horarios'))
    <x-nav-dropdown title="Reportes">
        @if(Auth::user()->hasModule('horarios'))
            <x-dropdown-item>Importar Horarios</x-dropdown-item>
        @endif
        @if(Auth::user()->hasModule('estadisticas'))
            <x-dropdown-item>Estadísticas</x-dropdown-item>
        @endif
    </x-nav-dropdown>
@endif
```

**⚠️ PROBLEMAS DETECTADOS:**

1. **"Importar Horarios" usa módulo incorrecto:**
   - En navegación verifica: `Auth::user()->hasModule('horarios')`
   - Debería verificar: `Auth::user()->hasModule('importacion')`
   - **Módulo `importacion` existe en availableModules() pero NO se usa en navegación**

2. **Falta el módulo `bitacora` en navegación:**
   - Módulo existe en RoleModule::availableModules()
   - NO aparece en ningún paquete de navegación
   - Admin puede verlo (línea 103) pero roles personalizados NO

3. **Lógica de mostrar paquetes con OR:**
   - Si un usuario tiene SOLO 1 módulo del paquete, el dropdown se muestra
   - Pero dentro del dropdown solo aparece ese módulo
   - Esto es **CORRECTO** pero puede confundir al usuario

---

## 🎯 2. PLAN DE ACCIÓN

### Objetivo:
Corregir la navegación para que los módulos asignados a un rol se muestren correctamente en sus paquetes correspondientes.

### Cambios Necesarios:

#### ✅ Cambio 1: Agregar módulo `bitacora` al Paquete 3 (Reportes)
**Ubicación:** `navigation.blade.php` - Sección Admin (línea ~103) y Custom Roles (línea ~185)

**Antes:**
```blade
@if(Auth::user()->hasModule('estadisticas') || Auth::user()->hasModule('horarios'))
```

**Después:**
```blade
@if(Auth::user()->hasModule('bitacora') || Auth::user()->hasModule('estadisticas') || Auth::user()->hasModule('importacion'))
```

**Items del dropdown:**
- Bitácora (verifica módulo `bitacora`)
- Importar Horarios (verifica módulo `importacion`)
- Estadísticas (verifica módulo `estadisticas`)

---

#### ✅ Cambio 2: Corregir verificación de "Importar Horarios"
**Ubicación:** `navigation.blade.php` - Líneas ~113 (Admin) y ~191 (Custom Roles)

**Antes:**
```blade
@if(Auth::user()->hasModule('horarios'))
    <x-dropdown-item :href="route('horarios.import')">
        Importar Horarios
    </x-dropdown-item>
@endif
```

**Después:**
```blade
@if(Auth::user()->hasModule('importacion'))
    <x-dropdown-item :href="route('horarios.import')">
        Importar Horarios
    </x-dropdown-item>
@endif
```

---

#### ✅ Cambio 3: Agregar íconos faltantes en dropdown items
**Ubicación:** Custom Roles section - Líneas 150-180

Los dropdown items de roles personalizados NO tienen íconos (a diferencia de la sección Admin).

**Agregar slot de íconos a cada dropdown-item.**

---

### 📊 Mapeo Final de Paquetes

| Paquete | Módulos Incluidos | Condición de Mostrar |
|---------|------------------|---------------------|
| **Usuarios y Roles** | `usuarios`, `roles` | hasModule('usuarios') \|\| hasModule('roles') |
| **Periodo Académico** | `docentes`, `materias`, `aulas`, `grupos`, `semestres`, `horarios` | hasModule('docentes') \|\| hasModule('materias') \|\| ... (6 módulos) |
| **Reportes** | `bitacora`, `importacion`, `estadisticas` | hasModule('bitacora') \|\| hasModule('importacion') \|\| hasModule('estadisticas') |

---

## 🔧 3. IMPLEMENTACIÓN

### Archivos a Modificar:
1. ✅ `resources/views/layouts/navigation.blade.php`
   - Actualizar condición del Paquete 3 (Admin)
   - Actualizar condición del Paquete 3 (Custom Roles)
   - Cambiar verificación de "Importar Horarios"
   - Agregar item "Bitácora" al Paquete 3

### NO es necesario modificar:
- ❌ Base de datos
- ❌ Modelos (User, Role, RoleModule)
- ❌ Controladores
- ❌ Formularios de crear/editar roles

---

## ✅ 4. VERIFICACIÓN POST-IMPLEMENTACIÓN

### Escenarios de Prueba:

**Escenario 1: Usuario con rol "admin"**
- ✅ Ver todos los paquetes
- ✅ Ver todos los módulos dentro de cada paquete

**Escenario 2: Usuario con módulos `usuarios` + `roles`**
- ✅ Ver Paquete "Usuarios y Roles" con 2 items
- ❌ NO ver otros paquetes

**Escenario 3: Usuario con módulos `docentes` + `materias`**
- ✅ Ver Paquete "Periodo Académico" con 2 items
- ❌ NO ver otros paquetes

**Escenario 4: Usuario con módulo `importacion` solamente**
- ✅ Ver Paquete "Reportes" con 1 item (Importar Horarios)
- ❌ NO ver otros paquetes

**Escenario 5: Usuario con módulos `bitacora` + `estadisticas`**
- ✅ Ver Paquete "Reportes" con 2 items
- ❌ NO ver otros paquetes

**Escenario 6: Usuario con módulos `horarios` + `importacion` + `estadisticas`**
- ✅ Ver Paquete "Periodo Académico" (solo Horarios)
- ✅ Ver Paquete "Reportes" (Importar Horarios + Estadísticas)

---

## 📝 CONCLUSIÓN

El problema NO está en el sistema de roles ni en la base de datos, sino en la **navegación**:

1. El módulo `importacion` existe pero se verificaba con `horarios`
2. El módulo `bitacora` existe pero NO estaba en la navegación de roles personalizados
3. La lógica de paquetes es correcta (muestra el paquete si tiene al menos 1 módulo)

**Solución:** Ajustar `navigation.blade.php` para mapear correctamente los 11 módulos a los 3 paquetes.
