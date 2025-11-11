# Sistema de Roles y Permisos - Documentación

## Configuración Completada ✅

### 1. Permisos Disponibles

El sistema ahora cuenta con **53 permisos** organizados en los siguientes módulos:

#### Usuarios (4 permisos)
- `ver_usuarios` - Ver listado de usuarios
- `crear_usuarios` - Crear nuevos usuarios
- `editar_usuarios` - Editar usuarios existentes
- `eliminar_usuarios` - Eliminar usuarios

#### Roles (4 permisos)
- `ver_roles` - Ver listado de roles
- `crear_roles` - Crear nuevos roles
- `editar_roles` - Editar roles existentes
- `eliminar_roles` - Eliminar roles

#### Docentes (4 permisos)
- `ver_docentes` - Ver listado de docentes
- `crear_docentes` - Crear nuevos docentes
- `editar_docentes` - Editar docentes existentes
- `eliminar_docentes` - Eliminar docentes

#### Materias (4 permisos)
- `ver_materias` - Ver listado de materias
- `crear_materias` - Crear nuevas materias
- `editar_materias` - Editar materias existentes
- `eliminar_materias` - Eliminar materias

#### Aulas (4 permisos)
- `ver_aulas` - Ver listado de aulas
- `crear_aulas` - Crear nuevas aulas
- `editar_aulas` - Editar aulas existentes
- `eliminar_aulas` - Eliminar aulas

#### Grupos (4 permisos)
- `ver_grupos` - Ver grupos
- `crear_grupos` - Crear grupos
- `editar_grupos` - Editar grupos
- `eliminar_grupos` - Eliminar grupos

#### Semestres (4 permisos)
- `ver_semestres` - Ver semestres
- `crear_semestres` - Crear semestres
- `editar_semestres` - Editar semestres
- `eliminar_semestres` - Eliminar semestres

#### Horarios (4 permisos)
- `ver_horarios` - Ver horarios
- `crear_horarios` - Crear horarios
- `editar_horarios` - Editar horarios
- `eliminar_horarios` - Eliminar horarios

#### Asistencias (3 permisos)
- `ver_asistencias` - Ver registros de asistencia
- `crear_asistencias` - Registrar asistencias
- `eliminar_asistencias` - Eliminar registros de asistencia

#### Estadísticas (3 permisos)
- `ver_estadisticas` - Ver estadísticas del sistema
- `ver_estadisticas_propias` - Ver solo estadísticas propias
- `exportar_estadisticas` - Exportar reportes de estadísticas

#### Reportes (3 permisos)
- `exportar_horarios` - Exportar horarios semanales
- `exportar_asistencias` - Exportar reportes de asistencia
- `ver_reportes` - Acceso a módulo de reportes

#### Sistema (3 permisos)
- `acceso_dashboard` - Acceder al dashboard
- `gestionar_perfil` - Editar perfil propio
- `importar_datos` - Importar datos masivos

---

## 2. Middleware Implementado

### CheckPermission Middleware
Ubicación: `app/Http/Middleware/CheckPermission.php`

Este middleware verifica:
1. Si el usuario está autenticado
2. Si tiene el rol de 'admin' (acceso total automático)
3. Si tiene el permiso específico requerido

Uso en rutas:
```php
Route::middleware([CheckPermission::class.':ver_usuarios'])->group(function() {
    // Rutas protegidas
});
```

---

## 3. Dashboard Dinámico

### Para Admins
- Dashboard completo con todos los módulos (vistas con tabs)
- Acceso total a todas las funcionalidades

### Para Docentes
- Dashboard personalizado con horario semanal
- Marcar asistencia (manual y QR)
- Ver sus propias estadísticas

### Para Roles Personalizados
- Dashboard con tarjetas de módulos según permisos asignados
- Vista: `resources/views/dashboards/custom-role.blade.php`
- Solo muestra los módulos a los que tienen acceso

---

## 4. Navegación Dinámica

La navegación ahora se adapta según el rol y permisos:

- **Admin**: Ve todos los módulos
- **Docente**: Ve Dashboard, Marcar Asistencia y Mis Estadísticas
- **Roles Personalizados**: Solo ven los módulos para los que tienen permiso `ver_*`

Archivos modificados:
- `resources/views/layouts/navigation.blade.php` (desktop)
- Sección responsive también actualizada (mobile)

---

## 5. Rutas Protegidas

Todas las rutas de recursos ahora están protegidas por permisos específicos:

| Módulo | Rutas Protegidas |
|--------|------------------|
| Usuarios | `ver_usuarios`, `crear_usuarios`, `editar_usuarios`, `eliminar_usuarios` |
| Roles | `ver_roles`, `crear_roles`, `editar_roles`, `eliminar_roles` |
| Docentes | `ver_docentes`, `crear_docentes`, `editar_docentes`, `eliminar_docentes` |
| Materias | `ver_materias`, `crear_materias`, `editar_materias`, `eliminar_materias` |
| Aulas | `ver_aulas`, `crear_aulas`, `editar_aulas`, `eliminar_aulas` |
| Grupos | `ver_grupos`, `crear_grupos`, `editar_grupos`, `eliminar_grupos` |
| Semestres | `ver_semestres`, `crear_semestres`, `editar_semestres`, `eliminar_semestres` |
| Horarios | `ver_horarios`, `crear_horarios`, `editar_horarios`, `eliminar_horarios` |
| Estadísticas | `ver_estadisticas` |

---

## 6. Scripts Disponibles

### Asignar todos los permisos al admin
```bash
php scripts/assign-admin-permissions.php
```

Este script:
- Busca el rol 'admin'
- Asigna TODOS los permisos disponibles
- Muestra un reporte detallado por módulo

---

## 7. Cómo Crear un Rol Personalizado

### Paso 1: Crear el Rol
1. Ir a "Roles" en el menú de administración
2. Click en "Crear Nuevo Rol"
3. Llenar:
   - Nombre (ej: "Secretario")
   - Descripción (ej: "Personal administrativo")
   - Nivel (número para jerarquía)
   - Estado (Activo/Inactivo)

### Paso 2: Asignar Permisos
1. En la lista de roles, click en "Editar"
2. Seleccionar los permisos que deseas asignar
3. Los permisos están organizados por módulo
4. Guardar cambios

### Paso 3: Asignar el Rol a un Usuario
1. Ir a "Usuarios"
2. Editar el usuario deseado
3. Seleccionar el rol personalizado
4. Guardar cambios

### Paso 4: El Usuario Verá
- Dashboard personalizado con tarjetas de los módulos permitidos
- Navegación solo con los módulos a los que tiene acceso
- Restricciones en acciones según permisos (crear, editar, eliminar)

---

## 8. Ejemplos de Uso

### Ejemplo 1: Rol de "Secretario Académico"
Permisos recomendados:
- ✅ ver_docentes, crear_docentes, editar_docentes
- ✅ ver_materias, crear_materias, editar_materias
- ✅ ver_grupos, crear_grupos, editar_grupos
- ✅ ver_horarios, crear_horarios, editar_horarios
- ❌ eliminar_* (sin permisos de eliminación)
- ❌ ver_usuarios, ver_roles (sin acceso a gestión de usuarios)

### Ejemplo 2: Rol de "Coordinador"
Permisos recomendados:
- ✅ ver_docentes, ver_materias, ver_grupos, ver_horarios
- ✅ ver_estadisticas, ver_reportes
- ✅ exportar_horarios, exportar_asistencias
- ❌ crear_*, editar_*, eliminar_* (solo lectura)

### Ejemplo 3: Rol de "Supervisor"
Permisos recomendados:
- ✅ ver_estadisticas, exportar_estadisticas
- ✅ ver_asistencias
- ✅ ver_docentes, ver_horarios
- ❌ Todo lo relacionado con creación/edición

---

## 9. Verificación del Sistema

Para verificar que todo funciona correctamente:

```bash
# 1. Ver todas las rutas registradas
php artisan route:list

# 2. Actualizar permisos
php artisan db:seed --class=PermissionSeeder

# 3. Asignar permisos al admin
php scripts/assign-admin-permissions.php
```

---

## 10. Notas Importantes

⚠️ **Roles del Sistema**
- Los roles `admin` y `docente` NO se pueden eliminar (son del sistema)
- Los roles personalizados SÍ se pueden editar y eliminar

⚠️ **Permisos del Admin**
- El rol admin tiene acceso automático a TODO
- No necesita permisos asignados explícitamente (pero el script los asigna para claridad)

⚠️ **Dashboard Dinámico**
- Solo usuarios que NO son admin ni docente ven el dashboard con tarjetas
- Admin y Docente tienen sus propios dashboards especializados

⚠️ **Navegación**
- Se actualiza automáticamente según permisos
- No requiere configuración manual

---

## 11. Archivos Modificados

### Modelos
- `app/Models/User.php` - Método `hasPermission()`
- `app/Models/Role.php` - Relación con permisos
- `app/Models/Permission.php` - Modelo base

### Middleware
- `app/Http/Middleware/CheckPermission.php` - Verificación de permisos
- `bootstrap/app.php` - Registro del middleware

### Controladores
- `app/Http/Controllers/DashboardController.php` - Dashboard dinámico

### Vistas
- `resources/views/dashboards/custom-role.blade.php` - Dashboard con tarjetas
- `resources/views/layouts/navigation.blade.php` - Navegación dinámica

### Rutas
- `routes/web.php` - Protección con middleware de permisos

### Base de Datos
- `database/seeders/PermissionSeeder.php` - 53 permisos

### Scripts
- `scripts/assign-admin-permissions.php` - Asignación automática

---

## ✅ Sistema Completamente Funcional

El sistema de roles y permisos está completamente implementado y listo para usar. Ahora puedes:

1. ✅ Crear roles personalizados
2. ✅ Asignar permisos granulares
3. ✅ Usuarios ven solo lo que tienen permitido
4. ✅ Navegación dinámica según permisos
5. ✅ Dashboard adaptado a cada rol
6. ✅ Protección de rutas automática
7. ✅ Admin con acceso total
8. ✅ Docentes con funciones específicas
9. ✅ Roles personalizados con permisos configurables
