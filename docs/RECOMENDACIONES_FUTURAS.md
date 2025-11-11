# 🔮 RECOMENDACIONES FUTURAS

> **Documento**: Guía para continuar mejorando el proyecto  
> **Fecha**: <?= date('Y-m-d') ?>  
> **Base**: Proyecto optimizado tras correcciones

---

## 📋 PRÓXIMOS PASOS RECOMENDADOS

### 1. ASIGNACIÓN DE MÓDULOS (PRIORIDAD ALTA)

**Situación Actual:**
- Solo 2 módulos asignados (al rol "coordinador")
- Sistema de módulos implementado pero subutilizado

**Acciones Recomendadas:**

```php
// Ejemplo: Asignar módulos al rol coordinador completo
$coordinador = Role::where('name', 'coordinador')->first();

$modulos = [
    'usuarios',
    'roles',
    'docentes',
    'materias',
    'aulas',
    'grupos',
    'semestres',
    'horarios',
    'estadisticas',
];

foreach ($modulos as $modulo) {
    RoleModule::firstOrCreate([
        'role_id' => $coordinador->id,
        'module_name' => $modulo,
    ]);
}
```

**Beneficios:**
- Roles personalizados funcionales
- Control granular de accesos
- Seguridad mejorada

---

### 2. TESTING Y VALIDACIÓN (PRIORIDAD ALTA)

**Tests a Crear:**

#### Tests de Módulos
```php
// tests/Feature/ModuleAccessTest.php
public function test_coordinador_can_access_assigned_modules()
{
    $coordinador = User::factory()->create();
    $coordinador->roles()->attach(Role::where('name', 'coordinador')->first());
    
    $this->actingAs($coordinador)
         ->get(route('usuarios.index'))
         ->assertOk();
}

public function test_user_cannot_access_unassigned_modules()
{
    $user = User::factory()->create();
    // Usuario sin módulos asignados
    
    $this->actingAs($user)
         ->get(route('usuarios.index'))
         ->assertForbidden();
}
```

#### Tests de Navegación
```php
// tests/Feature/NavigationTest.php
public function test_navigation_shows_only_allowed_modules()
{
    $coordinador = User::factory()->create();
    // Asignar solo módulo 'usuarios'
    
    $response = $this->actingAs($coordinador)->get('/dashboard');
    
    $response->assertSee('Usuarios');
    $response->assertDontSee('Docentes');
}
```

#### Tests de Asistencia
```php
// tests/Feature/AsistenciaTest.php
public function test_estado_is_always_lowercase()
{
    $asistencia = Asistencia::create([
        'estado' => 'PRESENTE', // Intento de capitalización
        // ... otros campos
    ]);
    
    $this->assertEquals('presente', $asistencia->estado);
}
```

---

### 3. DOCUMENTACIÓN DEL SISTEMA DE MÓDULOS (PRIORIDAD MEDIA)

**Crear**: `docs/GUIA_MODULOS.md`

Incluir:

1. **Lista de Módulos Disponibles**
   ```markdown
   | Módulo | Descripción | Rutas Protegidas |
   |--------|-------------|------------------|
   | usuarios | Gestión de usuarios | /users/* |
   | roles | Gestión de roles | /roles/* |
   | docentes | Gestión de docentes | /docentes/* |
   | ... | ... | ... |
   ```

2. **Cómo Asignar Módulos a un Rol**
   - Vía interfaz web
   - Vía consola
   - Vía seeder

3. **Cómo Crear Nuevos Módulos**
   ```php
   // 1. Agregar a RoleModule::availableModules()
   // 2. Proteger rutas con middleware('module:nombre')
   // 3. Agregar en navegación con hasModule('nombre')
   ```

---

### 4. MEJORAS EN LA INTERFAZ (PRIORIDAD MEDIA)

#### 4.1. Dashboard Mejorado
- Mostrar módulos disponibles del usuario
- Accesos rápidos a módulos asignados
- Estadísticas personalizadas por rol

#### 4.2. Gestión de Roles - Vista de Módulos
- Checkbox list de módulos disponibles
- Vista previa de permisos al asignar módulos
- Validación de módulos mínimos requeridos

#### 4.3. Feedback Visual
- Indicadores de módulos activos en navegación
- Tooltips explicativos
- Mensajes de error personalizados por módulo

---

### 5. OPTIMIZACIONES DE RENDIMIENTO (PRIORIDAD BAJA)

#### 5.1. Caché de Módulos
```php
// app/Models/User.php
public function hasModule(string $moduleName): bool
{
    return Cache::remember("user.{$this->id}.modules", 3600, function() use ($moduleName) {
        if ($this->hasRole('admin')) {
            return true;
        }
        
        return $this->roles()->whereHas('modules', function($query) use ($moduleName) {
            $query->where('module_name', $moduleName);
        })->exists();
    });
}
```

#### 5.2. Eager Loading
```php
// En controladores que usan módulos
$user = auth()->user()->load('roles.modules');
```

#### 5.3. Índices de Base de Datos
```php
// Migration
Schema::table('role_modules', function (Blueprint $table) {
    $table->index(['role_id', 'module_name']);
});
```

---

### 6. SEGURIDAD ADICIONAL (PRIORIDAD MEDIA)

#### 6.1. Auditoría de Accesos
```php
// Crear tabla audit_logs
// Registrar cada acceso a módulos protegidos
Log::channel('audit')->info('Module access', [
    'user_id' => auth()->id(),
    'module' => $moduleName,
    'action' => 'view',
    'ip' => request()->ip(),
]);
```

#### 6.2. Validación de Módulos en Middleware
```php
// Verificar que el módulo existe antes de validar acceso
if (!in_array($moduleName, RoleModule::availableModules())) {
    abort(404, 'Módulo no encontrado');
}
```

#### 6.3. Rate Limiting por Módulo
```php
// Limitar intentos de acceso a módulos restringidos
RateLimiter::for('module-access', function (Request $request) {
    return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
});
```

---

### 7. MIGRACIÓN DE DATOS (SI APLICABLE)

Si tienes datos antiguos del sistema de permisos:

```php
// Script: scripts/migrate-permissions-to-modules.php

// Mapeo de permisos antiguos a módulos nuevos
$permissionModuleMap = [
    'ver_usuarios' => 'usuarios',
    'crear_usuarios' => 'usuarios',
    'editar_usuarios' => 'usuarios',
    'eliminar_usuarios' => 'usuarios',
    // ... más mapeos
];

// Obtener roles con permisos antiguos (si hay backup)
// Crear módulos equivalentes
// Asignar módulos a roles
```

---

### 8. MONITOREO Y MANTENIMIENTO

#### 8.1. Script de Salud del Sistema
```php
// scripts/health-check.php
// - Verificar integridad de módulos
// - Validar que todos los usuarios tienen roles
// - Revisar rutas sin protección
// - Detectar módulos huérfanos
```

#### 8.2. Comando Artisan
```php
// php artisan modules:list
// Listar todos los módulos disponibles

// php artisan modules:assign {role} {module}
// Asignar módulo a rol desde consola

// php artisan modules:revoke {role} {module}
// Revocar módulo de rol
```

---

## 🎯 PLAN DE IMPLEMENTACIÓN SUGERIDO

### Semana 1: Configuración Básica
- ✅ Asignar módulos a roles existentes
- ✅ Crear tests básicos
- ✅ Documentar módulos disponibles

### Semana 2: Mejoras de Interfaz
- ✅ Dashboard personalizado
- ✅ Gestión visual de módulos
- ✅ Feedback mejorado

### Semana 3: Seguridad y Auditoría
- ✅ Implementar auditoría
- ✅ Rate limiting
- ✅ Validaciones adicionales

### Semana 4: Optimización
- ✅ Caché de módulos
- ✅ Índices de BD
- ✅ Comandos Artisan

---

## 📚 RECURSOS ÚTILES

- **Laravel Policies**: Para reglas más complejas de autorización
- **Laravel Gates**: Para lógica de autorización inline
- **Spatie Permission**: Package alternativo (si se necesita más funcionalidad)
- **Laravel Debugbar**: Para monitorear queries en desarrollo

---

## ⚠️ PRECAUCIONES

1. **Backup antes de cambios grandes**
   ```bash
   pg_dump -U usuario materia > backup_$(date +%Y%m%d).sql
   ```

2. **Probar en desarrollo primero**
   - Usar ambiente de desarrollo separado
   - Validar con datos de prueba
   - Ejecutar suite de tests completa

3. **Migrar gradualmente**
   - No cambiar todo el sistema a la vez
   - Implementar módulo por módulo
   - Mantener retrocompatibilidad temporal si es necesario

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

Antes de implementar cada mejora:

- [ ] Crear backup de base de datos
- [ ] Crear tests para la funcionalidad
- [ ] Documentar los cambios
- [ ] Probar en desarrollo
- [ ] Revisar con el equipo
- [ ] Desplegar en staging
- [ ] Validar en staging
- [ ] Desplegar en producción
- [ ] Monitorear por 24-48h

---

**Nota**: Este documento es una guía de mejora continua. Prioriza según las necesidades del proyecto.

---

**Última actualización**: <?= date('Y-m-d H:i:s') ?>  
**Versión del Proyecto**: Laravel 12.34.0 | PHP 8.4.10
