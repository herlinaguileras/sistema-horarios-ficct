# 🚀 INICIO RÁPIDO - MÓDULO DE BITÁCORA

## 📋 REQUISITOS PREVIOS

✅ Laravel 11 instalado  
✅ MySQL configurado  
✅ Usuario con rol "admin" creado  
✅ Permisos de escritura en `storage/`

---

## ⚡ ACCESO RÁPIDO (1 MINUTO)

### 1. Login como Administrador
```
URL: http://tu-dominio.com/login
Usuario: admin@example.com
Contraseña: tu_contraseña_admin
```

### 2. Navegar al Módulo
```
Click en menú: "🔒 Bitácora"
```

### 3. ¡Listo!
Verás el listado de registros de auditoría

---

## 🎯 RUTAS DISPONIBLES

| Ruta | Descripción | Método |
|------|-------------|--------|
| `/audit-logs` | Listado con filtros | GET |
| `/audit-logs/{id}` | Detalle de registro | GET |
| `/audit-logs/statistics` | Dashboard estadísticas | GET |
| `/audit-logs/export` | Exportar CSV | GET |

---

## 🔍 FUNCIONALIDADES PRINCIPALES

### 📋 Ver Registros
1. Click en "🔒 Bitácora"
2. **Filtrar (opcional):**
   - Usuario
   - Acción (CREATE, UPDATE, DELETE, etc.)
   - IP
   - Rango de fechas
   - Endpoint
3. Click "Filtrar" o "Limpiar Filtros"

### 📥 Exportar CSV
1. Aplicar filtros (opcional)
2. Click "📥 Exportar CSV"
3. Confirmar en el diálogo
4. Esperar descarga

### 🔍 Ver Detalle
1. En listado, click botón "Ver" en cualquier fila
2. Ver información completa:
   - Usuario que realizó la acción
   - Acción ejecutada
   - Navegador y Sistema Operativo
   - Request/Response JSON

### 📊 Ver Estadísticas
1. Click "📊 Estadísticas" (arriba a la derecha)
2. Ver:
   - 4 métricas clave
   - Gráfico de actividad (30 días)
   - Top acciones, usuarios, endpoints, IPs

---

## 📱 VISTA MÓVIL

**En dispositivos móviles (<768px):**
- Los registros se muestran como **tarjetas individuales**
- Filtros en **columna única**
- Botones **touch-friendly**
- Información **esencial visible**

**En desktop (≥768px):**
- Tabla completa con **todas las columnas**
- Filtros en **grid de 3 columnas**
- Más información visible simultáneamente

---

## 🎨 COMPONENTES REUTILIZABLES

### Badge de Acción
```blade
<x-audit.action-badge :action="'CREATE_USER'" />
```
**Tipos soportados:** CREATE, UPDATE, DELETE, LOGIN, LOGOUT, IMPORT, EXPORT, custom

### Breadcrumbs
```blade
<x-audit.breadcrumbs :items="[
    ['label' => 'Inicio', 'url' => '/'],
    ['label' => 'Bitácora', 'url' => route('audit-logs.index')],
    ['label' => 'Detalle']
]" />
```

### Badge de Método HTTP
```blade
<x-audit.http-method-badge :method="'POST'" />
```
**Métodos:** POST, GET, PUT, PATCH, DELETE

### Diálogo de Confirmación
```blade
<x-audit.confirm-dialog message="¿Eliminar?">
    <template x-slot:trigger>
        <button @click="open = true">Eliminar</button>
    </template>
    <template x-slot:confirm="{ close }">
        <form method="POST" @submit="close()">
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">
                Confirmar
            </button>
        </form>
    </template>
</x-audit.confirm-dialog>
```

---

## 🛠️ AGREGAR LOGGING A NUEVO CONTROLADOR

### Paso 1: Importar Modelo
```php
use App\Models\AuditLog;
```

### Paso 2: Registrar Acción
```php
// Ejemplo en método store()
public function store(Request $request)
{
    // Tu lógica de creación
    $estudiante = Estudiante::create($request->all());
    
    // Registrar en bitácora
    AuditLog::create([
        'user_id' => auth()->id(),
        'action' => 'CREATE_ESTUDIANTE',
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'endpoint' => 'estudiantes',
        'http_method' => 'POST',
        'request_data' => $request->all(),
        'response_data' => ['estudiante_id' => $estudiante->id]
    ]);
    
    return redirect()->back()->with('success', 'Estudiante creado');
}
```

### Paso 3: Nombrar Acciones (Convención)
```
CREATE_MODELO     → Creación de registro
UPDATE_MODELO     → Actualización
DELETE_MODELO     → Eliminación
IMPORT_MODELO     → Importación masiva
EXPORT_MODELO     → Exportación
LOGIN             → Inicio de sesión
LOGOUT            → Cierre de sesión
CUSTOM_ACTION     → Acción personalizada
```

---

## 🔐 PERMISOS Y SEGURIDAD

### ¿Quién puede acceder?
✅ **Solo usuarios con rol "admin"**

### ¿Cómo se verifica?
```php
// En routes/web.php
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/audit-logs', [AuditLogController::class, 'index']);
    // ...
});
```

### ¿Qué pasa si no soy admin?
❌ **Error 403 Forbidden** o redirección al dashboard

---

## 📊 CAMPOS DEL MODELO AUDITLOG

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | ID único del registro |
| `user_id` | bigint | ID del usuario (puede ser null) |
| `action` | string | Tipo de acción (CREATE_USER, etc.) |
| `ip_address` | string | IP del cliente |
| `user_agent` | text | Navegador y OS del cliente |
| `endpoint` | string | Ruta accedida (ej: "docentes") |
| `http_method` | string | Método HTTP (POST, GET, etc.) |
| `request_data` | json | Datos enviados en el request |
| `response_data` | json | Datos de respuesta |
| `created_at` | timestamp | Fecha de creación |

---

## 🧪 PROBAR EL MÓDULO

### 1. Verificar Backend
```bash
# Ver rutas de auditoría
php artisan route:list --name=audit

# Ejecutar migraciones
php artisan migrate:status

# Ver última migración
php artisan migrate:status | grep audit_logs
```

### 2. Verificar Frontend
1. Navegar a `/audit-logs`
2. Aplicar filtros
3. Exportar CSV
4. Ver detalle de un registro
5. Ver estadísticas

### 3. Verificar Responsive
1. Abrir navegador
2. Presionar `F12` (DevTools)
3. Click en icono "Toggle device toolbar" (Ctrl+Shift+M)
4. Seleccionar "iPhone 12 Pro"
5. Verificar que muestra tarjetas en lugar de tabla

---

## 🐛 SOLUCIÓN DE PROBLEMAS

### No veo el link "Bitácora" en el menú
**Causa:** No tienes rol "admin"  
**Solución:** 
```php
// En tinker o seeder
$user = User::find(1);
$adminRole = Role::where('name', 'admin')->first();
$user->roles()->attach($adminRole->id);
```

### Error 403 al acceder a /audit-logs
**Causa:** Middleware role:admin bloqueando  
**Solución:** Verifica que tu usuario tenga el rol admin

### No se muestran registros
**Causa:** No hay actividad registrada  
**Solución:** Realiza acciones (crear docente, editar aula, etc.) y recarga

### Gráfico Chart.js no aparece
**Causa:** CDN bloqueado o JavaScript deshabilitado  
**Solución:** 
- Verifica conexión a Internet
- Abre consola del navegador (F12) y busca errores
- Verifica que Chart.js carga: `https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js`

### Exportación CSV descarga vacío
**Causa:** Filtros muy restrictivos  
**Solución:** Click en "Limpiar Filtros" e intenta nuevamente

---

## 📚 MÁS INFORMACIÓN

### Documentación Completa
- **Resumen Ejecutivo:** `docs/RESUMEN_BITACORA.md` ⭐ (LEER PRIMERO)
- **Plan de Implementación:** `docs/PLAN_FRONTEND_BITACORA.md`
- **Documentación Técnica:** `docs/FRONTEND_BITACORA_COMPLETO.md`
- **Índice General:** `docs/INDICE_DOCUMENTACION.md`

### Comandos Útiles
```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Limpiar cachés
php artisan optimize:clear

# Ver configuración de base de datos
php artisan config:show database

# Ejecutar seeders (si existen)
php artisan db:seed --class=AuditLogSeeder
```

---

## ✅ CHECKLIST DE INICIO

- [ ] He iniciado sesión como administrador
- [ ] Veo el link "🔒 Bitácora" en el menú
- [ ] Puedo acceder a `/audit-logs`
- [ ] Puedo ver registros en la tabla/tarjetas
- [ ] Los filtros funcionan correctamente
- [ ] Puedo exportar CSV
- [ ] Puedo ver el detalle de un registro
- [ ] Puedo ver las estadísticas con gráfico
- [ ] En móvil veo tarjetas (no tabla)
- [ ] Entiendo cómo agregar logging a mi controlador

---

## 🎉 ¡LISTO!

Ya estás preparado para usar el módulo de Bitácora.

**Próximos pasos recomendados:**
1. Explorar todas las funcionalidades
2. Leer `RESUMEN_BITACORA.md` para visión completa
3. Revisar componentes reutilizables para tu proyecto
4. Agregar logging a tus controladores personalizados

---

**¿Preguntas?** Revisa la documentación completa en `docs/`

**Desarrollado con ❤️ | Diciembre 2024 | v1.0.0**
