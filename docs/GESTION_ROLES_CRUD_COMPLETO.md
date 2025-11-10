# 📋 Gestión de Roles - CRUD Completo y Optimizado

**Fecha:** 2025-01-XX  
**Versión:** 1.0  
**Estado:** ✅ COMPLETO

---

## 📑 Índice

1. [Resumen Ejecutivo](#resumen-ejecutivo)
2. [Estructura de Base de Datos](#estructura-de-base-de-datos)
3. [Modelo Role](#modelo-role)
4. [Controlador RoleController](#controlador-rolecontroller)
5. [Rutas](#rutas)
6. [Vistas](#vistas)
7. [Validaciones](#validaciones)
8. [Optimizaciones Implementadas](#optimizaciones-implementadas)
9. [Pruebas de Funcionalidad](#pruebas-de-funcionalidad)

---

## 1. Resumen Ejecutivo

La **Gestión de Roles** permite al administrador crear, editar, listar, activar/desactivar y eliminar roles del sistema, así como asignar permisos a cada rol.

### Funcionalidades Implementadas

✅ **CREATE** - Crear nuevos roles con permisos  
✅ **READ** - Listar roles con búsqueda y filtros  
✅ **UPDATE** - Editar roles y sus permisos  
✅ **DELETE** - Eliminar roles (con protecciones)  
✅ **TOGGLE STATUS** - Activar/Desactivar roles  

### Características Principales

- 🔒 **Protección de Roles del Sistema** (admin, docente)
- 🔍 **Búsqueda case-insensitive** por nombre y descripción
- 🎯 **Filtro por estado** (Activo/Inactivo)
- 📊 **Contadores** de usuarios y permisos asignados
- 🔄 **Transacciones DB** para integridad de datos
- ⚡ **Eager Loading** para optimizar queries
- ✨ **Validaciones robustas** en backend y frontend

---

## 2. Estructura de Base de Datos

### Tabla `roles`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | BIGINT | Primary Key |
| `name` | VARCHAR(255) | Nombre único del rol |
| `description` | TEXT | Descripción del rol |
| `level` | INTEGER | Nivel de jerarquía (1-100) |
| `status` | ENUM | Estado: Activo/Inactivo |
| `created_at` | TIMESTAMP | Fecha de creación |
| `updated_at` | TIMESTAMP | Última actualización |

### Relaciones

```php
// Rol -> Usuarios (many-to-many)
roles ↔ role_user ↔ users

// Rol -> Permisos (many-to-many)
roles ↔ permission_role ↔ permissions
```

---

## 3. Modelo Role

**Ubicación:** `app/Models/Role.php`

### Propiedades

```php
protected $fillable = [
    'name',
    'description',
    'level',
    'status',
];

protected $casts = [
    'level' => 'integer',
];
```

### Relaciones

```php
public function users()
{
    return $this->belongsToMany(User::class, 'role_user');
}

public function permissions()
{
    return $this->belongsToMany(Permission::class, 'permission_role');
}
```

### Métodos Útiles

#### `hasPermission(string $permissionName): bool`
Verifica si el rol tiene un permiso específico.

```php
$role->hasPermission('usuarios.crear'); // true/false
```

#### `isSystemRole(): bool`
Determina si es un rol del sistema que no se puede eliminar.

```php
$role->isSystemRole(); // true para 'admin' y 'docente'
```

#### `isActive(): bool`
Verifica si el rol está activo.

```php
$role->isActive(); // true si status === 'Activo'
```

### Scopes

#### `scopeActive($query)`
Filtra solo roles activos.

```php
Role::active()->get();
```

#### `scopeOrderByLevel($query, $direction = 'desc')`
Ordena por nivel de jerarquía.

```php
Role::orderByLevel('desc')->get();
```

---

## 4. Controlador RoleController

**Ubicación:** `app/Http/Controllers/RoleController.php`

### Métodos CRUD

#### 1. `index(Request $request)` - READ/LIST

**Funcionalidad:**
- Lista todos los roles
- Búsqueda por nombre/descripción (case-insensitive)
- Filtro por estado (Activo/Inactivo)
- Muestra contadores de usuarios y permisos

**Query Optimizada:**

```php
$query = Role::withCount(['users', 'permissions']);

// Búsqueda
if ($request->filled('search')) {
    $search = $request->search;
    $query->where(function($q) use ($search) {
        $q->where('name', 'ILIKE', "%{$search}%")
          ->orWhere('description', 'ILIKE', "%{$search}%");
    });
}

// Filtro
if ($request->filled('status')) {
    $query->where('status', $request->status);
}

// Ordenar
$roles = $query->orderByLevel('desc')->orderBy('name')->get();
```

**Optimizaciones:**
- ✅ `withCount()` para evitar N+1 queries
- ✅ Uso de scope `orderByLevel()`
- ✅ ILIKE para PostgreSQL (case-insensitive)

---

#### 2. `create()` - FORMULARIO CREAR

**Funcionalidad:**
- Muestra formulario para crear rol
- Lista permisos agrupados por módulo

**Query:**

```php
$permissionsByModule = Permission::orderBy('module')
                                 ->orderBy('name')
                                 ->get()
                                 ->groupBy('module');
```

**Optimización:**
- ✅ Ordenado por módulo y nombre para mejor UX

---

#### 3. `store(Request $request)` - CREATE

**Funcionalidad:**
- Valida datos del formulario
- Crea el nuevo rol
- Sincroniza permisos
- Usa transacciones DB

**Validaciones:**

```php
$validated = $request->validate([
    'name' => [
        'required', 
        'string', 
        'max:255', 
        'unique:roles', 
        'regex:/^[a-z0-9_-]+$/'  // Minúsculas, números, guiones
    ],
    'description' => ['nullable', 'string', 'max:500'],
    'level' => ['required', 'integer', 'min:1', 'max:100'],
    'status' => ['required', 'in:Activo,Inactivo'],
    'permissions' => ['nullable', 'array'],
    'permissions.*' => ['exists:permissions,id'],
]);
```

**Proceso con Transacción:**

```php
try {
    DB::beginTransaction();

    // Crear rol
    $role = Role::create([...]);

    // Sincronizar permisos
    if (!empty($validated['permissions'])) {
        $role->permissions()->sync($validated['permissions']);
    }

    DB::commit();
    return redirect()->route('roles.index')
        ->with('status', '✅ ¡Rol creado exitosamente!');

} catch (\Exception $e) {
    DB::rollBack();
    return back()->withInput()
        ->withErrors(['error' => 'Error al crear el rol']);
}
```

**Optimizaciones:**
- ✅ Transacciones DB para integridad
- ✅ Validación regex para formato de nombre
- ✅ Mensajes personalizados en español
- ✅ Manejo de excepciones con rollback

---

#### 4. `edit(Role $role)` - FORMULARIO EDITAR

**Funcionalidad:**
- Muestra formulario pre-llenado
- Carga permisos asignados
- Lista usuarios con este rol

**Query:**

```php
$permissionsByModule = Permission::orderBy('module')
                                 ->orderBy('name')
                                 ->get()
                                 ->groupBy('module');

$role->load(['permissions', 'users']);
```

**Optimización:**
- ✅ Eager loading para evitar N+1

---

#### 5. `update(Request $request, Role $role)` - UPDATE

**Funcionalidad:**
- Valida datos (permite editar nombre de roles del sistema)
- Actualiza el rol
- Sincroniza permisos
- Usa transacciones DB

**Validación Especial:**

```php
$nameRules = ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)];

// Solo aplicar regex si NO es rol del sistema
if (!$role->isSystemRole()) {
    $nameRules[] = 'regex:/^[a-z0-9_-]+$/';
}
```

**Proceso:**

```php
try {
    DB::beginTransaction();

    $role->update([...]);
    $role->permissions()->sync($validated['permissions'] ?? []);

    DB::commit();
    return redirect()->route('roles.index')
        ->with('status', '✅ ¡Rol actualizado exitosamente!');

} catch (\Exception $e) {
    DB::rollBack();
    return back()->withInput()
        ->withErrors(['error' => 'Error al actualizar el rol']);
}
```

**Optimizaciones:**
- ✅ Permite editar roles del sistema sin restricción de nombre
- ✅ Sync de permisos incluso si está vacío (desasigna todos)
- ✅ Transacciones DB

---

#### 6. `destroy(Role $role)` - DELETE

**Funcionalidad:**
- Verifica si es rol del sistema (no eliminar)
- Verifica si tiene usuarios asignados (no eliminar)
- Elimina relaciones con permisos
- Elimina el rol

**Proceso:**

```php
// Protección roles del sistema
if ($role->isSystemRole()) {
    return back()->withErrors([
        'error' => '❌ No puedes eliminar los roles del sistema (admin, docente).'
    ]);
}

// Verificar usuarios asignados
$usersCount = $role->users()->count();
if ($usersCount > 0) {
    return back()->withErrors([
        'error' => "❌ No puedes eliminar este rol porque tiene {$usersCount} usuario(s) asignado(s)."
    ]);
}

try {
    DB::beginTransaction();
    
    // Eliminar relaciones
    $role->permissions()->detach();
    
    // Eliminar rol
    $role->delete();
    
    DB::commit();
    return redirect()->route('roles.index')
        ->with('status', '✅ ¡Rol eliminado exitosamente!');

} catch (\Exception $e) {
    DB::rollBack();
    return back()->withErrors(['error' => 'Error al eliminar el rol']);
}
```

**Optimizaciones:**
- ✅ Usa método `isSystemRole()` del modelo
- ✅ Muestra cantidad exacta de usuarios asignados
- ✅ Detach automático de permisos
- ✅ Transacciones DB

---

#### 7. `toggleStatus(Role $role)` - EXTRA

**Funcionalidad:**
- Cambia estado de Activo a Inactivo y viceversa
- Protege roles del sistema de ser desactivados

**Proceso:**

```php
// Prevenir desactivar roles del sistema
if ($role->isSystemRole() && $role->isActive()) {
    return back()->withErrors([
        'error' => '❌ No puedes desactivar los roles del sistema.'
    ]);
}

$newStatus = $role->isActive() ? 'Inactivo' : 'Activo';
$role->update(['status' => $newStatus]);

return back()->with('status', "✅ Rol {$newStatus} correctamente.");
```

**Optimización:**
- ✅ Usa métodos `isSystemRole()` e `isActive()` del modelo
- ✅ Toggle rápido sin formulario completo

---

## 5. Rutas

**Ubicación:** `routes/web.php`

```php
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    // CRUD completo
    Route::resource('roles', RoleController::class)->except(['show']);
    
    // Ruta extra para toggle status
    Route::patch('/roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])
         ->name('roles.toggle-status');
});
```

### Rutas Generadas

| Método | URI | Acción | Nombre |
|--------|-----|--------|--------|
| GET | `/roles` | index | roles.index |
| GET | `/roles/create` | create | roles.create |
| POST | `/roles` | store | roles.store |
| GET | `/roles/{role}/edit` | edit | roles.edit |
| PUT/PATCH | `/roles/{role}` | update | roles.update |
| DELETE | `/roles/{role}` | destroy | roles.destroy |
| PATCH | `/roles/{role}/toggle-status` | toggleStatus | roles.toggle-status |

---

## 6. Vistas

### 6.1. `roles/index.blade.php` - LISTADO

**Ubicación:** `resources/views/roles/index.blade.php`

**Características:**

- 📋 Tabla con todas las columnas relevantes
- 🔍 Barra de búsqueda
- 🎯 Filtro por estado
- 🔢 Contadores de permisos y usuarios
- 🎨 Badges de colores para estados
- ⚡ Botón toggle status
- ✏️ Botón editar
- 🗑️ Botón eliminar (con protección)

**Estructura:**

```html
<table>
  <thead>
    <tr>
      <th>Nombre Rol</th>
      <th>Descripción</th>
      <th>Nivel</th>
      <th>Estado</th>
      <th>Permisos</th>
      <th>Usuarios</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>{{ $role->name }}</td>
      <td>{{ $role->description }}</td>
      <td>Nivel {{ $role->level }}</td>
      <td>
        <!-- Badge Activo/Inactivo -->
      </td>
      <td>{{ $role->permissions_count }} permiso(s)</td>
      <td>{{ $role->users_count }} usuario(s)</td>
      <td>
        <!-- Botones: Toggle Status, Editar, Eliminar -->
      </td>
    </tr>
  </tbody>
</table>
```

---

### 6.2. `roles/create.blade.php` - CREAR ROL

**Ubicación:** `resources/views/roles/create.blade.php`

**Campos del Formulario:**

1. **Nombre del Rol** (required)
   - Validación frontend: required
   - Placeholder: "ej: coordinador, secretaria"
   - Ayuda: "Usa minúsculas sin espacios"

2. **Nivel** (required)
   - Type: number
   - Min: 1, Max: 100
   - Default: 10
   - Ayuda: "Mayor nivel = mayor jerarquía (admin=100)"

3. **Descripción** (opcional)
   - Type: textarea
   - Max: 500 caracteres

4. **Estado** (required)
   - Type: select
   - Options: Activo, Inactivo
   - Default: Activo

5. **Permisos** (opcional)
   - Checkboxes agrupados por módulo
   - Todos los permisos disponibles

**Botones:**
- 🔙 **Cancelar** (gris, izquierda)
- 💾 **Crear Rol** (azul, derecha)

---

### 6.3. `roles/edit.blade.php` - EDITAR ROL

**Ubicación:** `resources/views/roles/edit.blade.php`

**Diferencias con Create:**

- Campos pre-llenados con `old()` y fallback a `$role`
- Campo nombre **readonly** para roles del sistema (admin, docente)
- Muestra lista de usuarios asignados al rol
- Pre-selecciona permisos actuales
- Botón: **💾 Guardar Cambios** (en lugar de "Crear Rol")

**Código para Nombre Readonly:**

```blade
<input type="text"
       name="name"
       id="name"
       value="{{ old('name', $role->name) }}"
       {{ in_array($role->name, ['admin', 'docente']) ? 'readonly' : '' }}
       required
       class="...">
```

**Sección de Usuarios Asignados:**

```blade
@if($role->users->count() > 0)
    <div class="mt-4">
        <h4>Usuarios con este rol:</h4>
        <ul>
            @foreach($role->users as $user)
                <li>{{ $user->name }} ({{ $user->email }})</li>
            @endforeach
        </ul>
    </div>
@endif
```

---

## 7. Validaciones

### 7.1. Backend (PHP)

#### Crear Rol (store)

```php
'name' => [
    'required',
    'string',
    'max:255',
    'unique:roles',
    'regex:/^[a-z0-9_-]+$/'  // Solo minúsculas, números, guiones
],
'description' => ['nullable', 'string', 'max:500'],
'level' => ['required', 'integer', 'min:1', 'max:100'],
'status' => ['required', 'in:Activo,Inactivo'],
'permissions' => ['nullable', 'array'],
'permissions.*' => ['exists:permissions,id'],
```

#### Actualizar Rol (update)

```php
'name' => [
    'required',
    'string',
    'max:255',
    Rule::unique('roles')->ignore($role->id),
    // Solo aplicar regex si NO es rol del sistema
    ...($role->isSystemRole() ? [] : ['regex:/^[a-z0-9_-]+$/'])
],
// ... resto igual a store
```

#### Mensajes Personalizados

```php
[
    'name.regex' => 'El nombre solo puede contener letras minúsculas, números, guiones y guiones bajos.',
    'name.unique' => 'Ya existe un rol con este nombre.',
    'description.max' => 'La descripción no puede exceder 500 caracteres.',
]
```

---

### 7.2. Frontend (HTML5)

```html
<!-- Nombre -->
<input type="text"
       name="name"
       required
       pattern="[a-z0-9_-]+"
       placeholder="ej: coordinador, secretaria">

<!-- Nivel -->
<input type="number"
       name="level"
       required
       min="1"
       max="100">

<!-- Estado -->
<select name="status" required>
    <option value="Activo">Activo</option>
    <option value="Inactivo">Inactivo</option>
</select>
```

---

## 8. Optimizaciones Implementadas

### 8.1. Base de Datos

✅ **Eager Loading**
```php
$role->load(['permissions', 'users']);
```

✅ **WithCount para Evitar N+1**
```php
Role::withCount(['users', 'permissions'])->get();
```

✅ **Índices** (ya implementados en migración)
- `name` (unique)
- `status`
- `level`

---

### 8.2. Código

✅ **Scopes Reutilizables**
```php
Role::active()->orderByLevel('desc')->get();
```

✅ **Métodos Helper en Modelo**
```php
$role->isSystemRole();
$role->isActive();
$role->hasPermission('usuarios.crear');
```

✅ **Transacciones DB**
```php
DB::beginTransaction();
// ... operaciones
DB::commit();
// o DB::rollBack() en catch
```

✅ **Validación con Rule Object**
```php
Rule::unique('roles')->ignore($role->id)
```

---

### 8.3. UX/UI

✅ **Búsqueda Case-Insensitive** (PostgreSQL ILIKE)
✅ **Filtros Persistentes** (mantiene valores en querystring)
✅ **Mensajes con Emojis** (✅ ❌ 💾 🗑️)
✅ **Confirmación antes de Eliminar**
✅ **Badges de Colores** para estados y contadores
✅ **Readonly para Campos Protegidos** (roles del sistema)
✅ **Ayudas Contextuales** (placeholders, hints)

---

## 9. Pruebas de Funcionalidad

### 9.1. Verificar Rutas

```bash
php artisan route:list --name=roles
```

**Resultado Esperado:** 7 rutas

---

### 9.2. Pruebas en Tinker

```bash
php artisan tinker
```

#### Crear Rol

```php
$role = Role::create([
    'name' => 'supervisor',
    'description' => 'Supervisor de área',
    'level' => 50,
    'status' => 'Activo'
]);
```

#### Asignar Permisos

```php
$permisos = Permission::whereIn('name', [
    'usuarios.ver',
    'docentes.ver',
    'materias.ver'
])->pluck('id');

$role->permissions()->sync($permisos);
```

#### Asignar Rol a Usuario

```php
$user = User::find(1);
$role = Role::where('name', 'supervisor')->first();
$user->roles()->attach($role->id);
```

#### Verificar Permisos

```php
$role->hasPermission('usuarios.ver'); // true
$role->hasPermission('usuarios.crear'); // false
```

#### Verificar Métodos

```php
$role->isSystemRole(); // false (supervisor)
$role->isActive(); // true

$admin = Role::where('name', 'admin')->first();
$admin->isSystemRole(); // true
```

---

### 9.3. Pruebas de Validación

#### Intentar Crear Rol con Nombre Duplicado

```bash
curl -X POST http://localhost/roles \
  -d "name=admin&description=Test&level=50&status=Activo"
```

**Resultado Esperado:** Error de validación "Ya existe un rol con este nombre."

---

#### Intentar Eliminar Rol del Sistema

```bash
curl -X DELETE http://localhost/roles/1  # (ID del rol 'admin')
```

**Resultado Esperado:** Error "No puedes eliminar los roles del sistema"

---

#### Intentar Eliminar Rol con Usuarios

```php
// En tinker
$supervisor = Role::where('name', 'supervisor')->first();
$supervisor->users()->count(); // > 0

// Intentar eliminar vía web
```

**Resultado Esperado:** Error "No puedes eliminar este rol porque tiene X usuario(s) asignado(s)."

---

### 9.4. Pruebas de UI

#### Búsqueda

1. Ir a `/roles`
2. Escribir "admin" en búsqueda
3. Click "Buscar"

**Resultado Esperado:** Solo muestra rol "admin"

---

#### Filtro por Estado

1. Ir a `/roles`
2. Seleccionar "Inactivo" en filtro
3. Click "Buscar"

**Resultado Esperado:** Solo roles inactivos

---

#### Toggle Status

1. Ir a `/roles`
2. Click botón "Desactivar" en rol "supervisor"

**Resultado Esperado:** 
- Botón cambia a "Activar"
- Badge cambia de verde (Activo) a rojo (Inactivo)
- Mensaje de éxito: "✅ Rol Inactivo correctamente."

---

#### Crear Rol

1. Click "+ Nuevo Rol"
2. Llenar formulario:
   - Nombre: `coordinador`
   - Descripción: "Coordinador académico"
   - Nivel: 60
   - Estado: Activo
   - Permisos: Seleccionar algunos checkboxes
3. Click "💾 Crear Rol"

**Resultado Esperado:**
- Redirección a `/roles`
- Mensaje: "✅ ¡Rol creado exitosamente!"
- Rol visible en tabla

---

#### Editar Rol

1. Click "✏️ Editar" en rol "coordinador"
2. Cambiar descripción
3. Agregar/quitar permisos
4. Click "💾 Guardar Cambios"

**Resultado Esperado:**
- Redirección a `/roles`
- Mensaje: "✅ ¡Rol actualizado exitosamente!"
- Cambios visibles

---

#### Eliminar Rol

1. Click "🗑️ Eliminar" en rol sin usuarios
2. Confirmar en popup

**Resultado Esperado:**
- Redirección a `/roles`
- Mensaje: "✅ ¡Rol eliminado exitosamente!"
- Rol ya no visible

---

## 10. Checklist Final ✅

### Backend
- [✅] Modelo `Role` con fillable, casts, relaciones
- [✅] Métodos helper: `isSystemRole()`, `isActive()`, `hasPermission()`
- [✅] Scopes: `active()`, `orderByLevel()`
- [✅] Controlador `RoleController` completo
- [✅] Método `index()` con búsqueda y filtros
- [✅] Método `create()` con permisos agrupados
- [✅] Método `store()` con transacciones y validaciones
- [✅] Método `edit()` con eager loading
- [✅] Método `update()` con validación especial para roles del sistema
- [✅] Método `destroy()` con protecciones múltiples
- [✅] Método `toggleStatus()` para cambiar estado
- [✅] Rutas resource + ruta extra toggle-status

### Frontend
- [✅] Vista `index.blade.php` con tabla completa
- [✅] Búsqueda y filtros funcionales
- [✅] Contadores de permisos y usuarios
- [✅] Badges de colores para estados
- [✅] Botón toggle status
- [✅] Protección visual de roles del sistema
- [✅] Vista `create.blade.php` con formulario completo
- [✅] Permisos agrupados por módulo
- [✅] Validaciones HTML5
- [✅] Vista `edit.blade.php` con campos pre-llenados
- [✅] Campo nombre readonly para roles del sistema
- [✅] Lista de usuarios asignados
- [✅] Botones con emojis y colores consistentes

### Validaciones
- [✅] Validación backend completa (name, description, level, status, permissions)
- [✅] Mensajes personalizados en español
- [✅] Validación frontend HTML5
- [✅] Confirmación JavaScript para eliminar

### Optimizaciones
- [✅] Eager loading para evitar N+1
- [✅] WithCount para contadores eficientes
- [✅] Transacciones DB en operaciones críticas
- [✅] Scopes reutilizables
- [✅] Métodos helper en modelo
- [✅] Búsqueda case-insensitive (ILIKE)

### Seguridad
- [✅] Middleware `role:admin` en todas las rutas
- [✅] CSRF tokens en formularios
- [✅] Protección de roles del sistema
- [✅] Validación de usuarios asignados antes de eliminar
- [✅] Manejo de excepciones con rollback

---

## 11. Conclusión

La **Gestión de Roles** está **100% completa y optimizada** con:

✅ **5 operaciones CRUD** (Create, Read, Update, Delete, List)  
✅ **1 operación extra** (Toggle Status)  
✅ **Validaciones robustas** en backend y frontend  
✅ **Optimizaciones de queries** (eager loading, withCount)  
✅ **Transacciones DB** para integridad  
✅ **Protecciones de seguridad** (roles del sistema, usuarios asignados)  
✅ **UX mejorada** (búsqueda, filtros, badges, confirmaciones)  
✅ **Código limpio y mantenible** (scopes, helpers, métodos reutilizables)  

### Próximos Pasos Sugeridos

1. ✅ **Gestión de Usuarios** (ya implementado)
2. ✅ **Gestión de Roles** (ESTE DOCUMENTO)
3. 🔄 **Gestión de Permisos** (revisar y optimizar)
4. ⏳ **Importación Excel/CSV de Usuarios**
5. ⏳ **CRUD de Semestres**

---

**Documentado por:** GitHub Copilot  
**Fecha:** 2025-01-XX  
**Versión Laravel:** 11.x  
**Versión PHP:** 8.3
