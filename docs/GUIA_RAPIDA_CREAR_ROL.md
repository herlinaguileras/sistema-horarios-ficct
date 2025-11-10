# 🚀 GUÍA RÁPIDA - Crear Rol "Coordinador"

## ✅ SÍ, el botón ya existe y funciona perfectamente

---

## 📍 Ubicación del Botón

1. Inicia sesión como **admin**
2. En el menú superior, click en **"Roles"**
3. En la esquina superior derecha verás:

```
┌─────────────────────────────────────────┐
│ GESTIÓN DE ROLES    [+ Nuevo Rol] ← AQUÍ│
└─────────────────────────────────────────┘
```

---

## 🎯 Flujo en 5 Pasos

### Paso 1: Click en "+ Nuevo Rol"
- Abre el formulario de creación

### Paso 2: Llenar Datos Básicos
```
Nombre:      coordinador      ← (minúsculas, sin espacios)
Nivel:       60               ← (1-100, mayor = más jerarquía)
Descripción: Coordinador académico
Estado:      ● Activo
```

### Paso 3: Seleccionar Permisos
```
✓ usuarios.ver
✓ docentes.ver
✓ docentes.crear
✓ docentes.editar
✓ materias.ver
✓ materias.crear
✓ grupos.ver
✓ horarios.ver
✓ asistencias.ver
✓ reportes.ver
✓ reportes.exportar
```

### Paso 4: Click en "💾 Crear Rol"
- Validación automática
- Creación en base de datos
- Asignación de permisos

### Paso 5: ¡Listo!
```
✅ ¡Rol creado exitosamente!

Ahora aparece en la tabla:
┌──────────────┬───────┬────────┬───────────┐
│ Nombre       │ Nivel │ Estado │ Permisos  │
├──────────────┼───────┼────────┼───────────┤
│ admin        │ 100   │ Activo │ 29        │
│ coordinador  │ 60    │ Activo │ 11        │ ← NUEVO
│ docente      │ 50    │ Activo │ 5         │
└──────────────┴───────┴────────┴───────────┘
```

---

## 🔧 Después de Crear el Rol

### Para Asignar el Rol a un Usuario:

1. Ir a **"Gestión de Usuarios"**
2. Click en **"+ Nuevo Usuario"** o **"✏️ Editar"**
3. En el formulario, en el campo **"Roles"**, seleccionar **"coordinador"**
4. **Guardar**

¡El usuario ahora tiene los permisos del coordinador!

---

## 📊 Rutas Disponibles (Confirmadas)

| Acción | Ruta | Método |
|--------|------|--------|
| Ver lista | `/roles` | GET |
| **Crear (formulario)** | **`/roles/create`** | **GET** ← Botón te lleva aquí |
| **Guardar** | **`/roles`** | **POST** ← Formulario envía aquí |
| Editar | `/roles/{id}/edit` | GET |
| Actualizar | `/roles/{id}` | PUT/PATCH |
| Eliminar | `/roles/{id}` | DELETE |
| Toggle estado | `/roles/{id}/toggle-status` | PATCH |

---

## 🎓 Ejemplos de Roles que Puedes Crear

| Nombre | Nivel | Descripción | Permisos Sugeridos |
|--------|-------|-------------|--------------------|
| `coordinador` | 60 | Coordinador académico | Ver todo, crear/editar docentes y materias |
| `secretaria` | 40 | Personal administrativo | Ver usuarios, docentes, horarios |
| `director` | 80 | Director de facultad | Casi todos los permisos (como admin) |
| `supervisor` | 55 | Supervisor de área | Ver todo, sin eliminar |
| `observador` | 10 | Solo consulta | Solo ver (sin crear/editar/eliminar) |

---

## ⚠️ Reglas Importantes

### Nombre del Rol
✅ **Correcto:** `coordinador`, `secretaria`, `supervisor_area`, `jefe-depto`  
❌ **Incorrecto:** `Coordinador`, `SECRETARIA`, `Supervisor Area` (mayúsculas o espacios)

### Nivel
- Admin: 100 (máximo)
- Nuevos roles: 1-99
- Docente: 50 (referencia)

### Restricciones
- ❌ NO puedes eliminar `admin` ni `docente` (roles del sistema)
- ❌ NO puedes eliminar roles que tengan usuarios asignados
- ✅ SÍ puedes editar permisos en cualquier momento
- ✅ SÍ puedes activar/desactivar roles

---

## 🔐 Seguridad

- ✅ Solo el **admin** puede gestionar roles
- ✅ Validaciones automáticas (nombre único, formato correcto)
- ✅ Transacciones de base de datos (si falla, se revierte)
- ✅ Protección de roles del sistema

---

## 📞 ¿Necesitas Ayuda?

**Documentación Completa:**
- Ver archivo: `docs/GESTION_ROLES_CRUD_COMPLETO.md`
- Ver guía de uso: `docs/GUIA_USO_GESTION_ROLES.md`

**Verificar que todo funciona:**
```bash
php artisan route:list --name=roles
```

**Resultado esperado:** 7 rutas (index, create, store, edit, update, destroy, toggle-status)

---

## 🎉 Resumen

✅ **El botón "+ Nuevo Rol" YA ESTÁ IMPLEMENTADO**  
✅ **Todo el sistema CRUD está funcionando**  
✅ **Puedes crear roles personalizados desde el panel**  
✅ **No necesitas tocar código ni base de datos**  

**Solo ingresa al sistema, ve a "Roles" y click en "+ Nuevo Rol"** 🚀

---

**Fecha:** 27 de Octubre, 2025  
**Sistema:** Laravel 11.x + PostgreSQL  
**Estado:** ✅ TOTALMENTE FUNCIONAL
