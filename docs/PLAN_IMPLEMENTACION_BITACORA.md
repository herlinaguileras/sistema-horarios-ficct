# PLAN DE IMPLEMENTACIÓN - SISTEMA DE BITÁCORA (AUDIT LOG)

**Fecha:** 12 de Noviembre de 2025  
**Proyecto:** Sistema de Horarios FICCT  
**Objetivo:** Implementar un sistema completo de auditoría que registre todas las acciones importantes del sistema

---

## 📋 ANÁLISIS DE LA SITUACIÓN ACTUAL

### ✅ Ya Implementado:
- ✅ Modelo `AuditLog` (`app/Models/AuditLog.php`)
- ✅ Migración de base de datos (`audit_logs` table)
- ✅ Uso parcial en `AsistenciaController`

### ❌ Faltante:
- ❌ Middleware de auditoría automática
- ❌ Trait reutilizable para controladores
- ❌ Sistema de registro en todos los controladores
- ❌ Captura del endpoint/ruta
- ❌ Vista administrativa de bitácora
- ❌ Filtros y búsqueda de logs

---

## 🎯 REQUERIMIENTOS ESPECÍFICOS

La bitácora DEBE capturar:

1. **IP Address** → `$request->ip()`
2. **Usuario** → `auth()->user()`
3. **Acción** → Descripción clara (CREATE, UPDATE, DELETE, etc.)
4. **Endpoint** → Ruta completa (`$request->path()` o `$request->fullUrl()`)
5. **Detalles adicionales** → Modelo afectado, cambios realizados
6. **User Agent** → Navegador/dispositivo
7. **Timestamp** → Fecha y hora exacta

---

## 📦 FASES DE IMPLEMENTACIÓN

### **FASE 1: Actualización de Base de Datos**
**Tiempo estimado:** 5 minutos

#### Objetivos:
- Agregar columna `endpoint` a la tabla `audit_logs`
- Asegurar que todos los campos necesarios existan

#### Archivos a modificar:
1. ✨ **CREAR:** `database/migrations/2025_11_12_000001_add_endpoint_to_audit_logs_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('endpoint')->nullable()->after('action');
            $table->string('http_method', 10)->nullable()->after('endpoint');
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['endpoint', 'http_method']);
        });
    }
};
```

#### Comandos a ejecutar:
```powershell
php artisan migrate
```

---

### **FASE 2: Actualización del Modelo AuditLog**
**Tiempo estimado:** 10 minutos

#### Objetivos:
- Actualizar el modelo para incluir los nuevos campos
- Agregar métodos helper para facilitar la creación de logs
- Agregar scopes para filtrado

#### Archivos a modificar:
1. 📝 **ACTUALIZAR:** `app/Models/AuditLog.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    use HasFactory;

    // Indicamos que NO usamos la columna 'updated_at'
    const UPDATED_AT = null;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'user_id',
        'action',
        'endpoint',
        'http_method',
        'model_type',
        'model_id',
        'details',
        'ip_address',
        'user_agent',
    ];

    /**
     * Casts de atributos
     */
    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Un registro de auditoría pertenece a un Usuario (el que hizo la acción).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Método estático para crear un log de auditoría de manera simplificada
     *
     * @param string $action
     * @param string|null $modelType
     * @param int|null $modelId
     * @param array|null $details
     * @return AuditLog
     */
    public static function logAction(
        string $action,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $details = null
    ): AuditLog {
        $request = request();
        
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'endpoint' => $request->path(),
            'http_method' => $request->method(),
            'model_type' => $modelType,
            'model_id' => $modelId,
            'details' => $details,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Scope: Filtrar por usuario
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Filtrar por acción
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', 'like', "%{$action}%");
    }

    /**
     * Scope: Filtrar por modelo
     */
    public function scopeByModel($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope: Filtrar por rango de fechas
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Filtrar por IP
     */
    public function scopeByIp($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Obtener el nombre del usuario de forma segura
     */
    public function getUserNameAttribute(): string
    {
        return $this->user ? $this->user->name : 'Usuario Eliminado';
    }

    /**
     * Formatear la acción para mostrar
     */
    public function getFormattedActionAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->action));
    }
}
```

---

### **FASE 3: Crear Trait para Auditoría**
**Tiempo estimado:** 15 minutos

#### Objetivos:
- Crear un trait reutilizable que facilite el registro de auditoría
- Evitar código duplicado en los controladores
- Proporcionar métodos simples y consistentes

#### Archivos a crear:
1. ✨ **CREAR:** `app/Traits/LogsActivity.php`

```php
<?php

namespace App\Traits;

use App\Models\AuditLog;

trait LogsActivity
{
    /**
     * Registrar una acción en la bitácora
     *
     * @param string $action Descripción de la acción (ej: 'CREATE_MATERIA', 'UPDATE_DOCENTE')
     * @param string|null $modelType Tipo de modelo afectado (ej: 'App\Models\Materia')
     * @param int|null $modelId ID del modelo afectado
     * @param array|null $details Detalles adicionales en formato array
     * @return AuditLog
     */
    protected function logActivity(
        string $action,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $details = null
    ): AuditLog {
        return AuditLog::logAction($action, $modelType, $modelId, $details);
    }

    /**
     * Log de creación
     */
    protected function logCreate($model, array $additionalDetails = []): AuditLog
    {
        $modelName = class_basename($model);
        
        return $this->logActivity(
            "CREATE_{$modelName}",
            get_class($model),
            $model->id,
            array_merge([
                'model' => $modelName,
                'model_id' => $model->id,
                'action_type' => 'create',
            ], $additionalDetails)
        );
    }

    /**
     * Log de actualización
     */
    protected function logUpdate($model, array $changes, array $additionalDetails = []): AuditLog
    {
        $modelName = class_basename($model);
        
        return $this->logActivity(
            "UPDATE_{$modelName}",
            get_class($model),
            $model->id,
            array_merge([
                'model' => $modelName,
                'model_id' => $model->id,
                'action_type' => 'update',
                'changes' => $changes,
            ], $additionalDetails)
        );
    }

    /**
     * Log de eliminación
     */
    protected function logDelete($model, array $additionalDetails = []): AuditLog
    {
        $modelName = class_basename($model);
        
        return $this->logActivity(
            "DELETE_{$modelName}",
            get_class($model),
            $model->id,
            array_merge([
                'model' => $modelName,
                'model_id' => $model->id,
                'action_type' => 'delete',
                'deleted_data' => $model->toArray(),
            ], $additionalDetails)
        );
    }

    /**
     * Log de login
     */
    protected function logLogin(?string $email = null): AuditLog
    {
        return $this->logActivity(
            'LOGIN',
            'App\Models\User',
            auth()->id(),
            [
                'action_type' => 'authentication',
                'email' => $email ?? auth()->user()->email,
            ]
        );
    }

    /**
     * Log de logout
     */
    protected function logLogout(): AuditLog
    {
        return $this->logActivity(
            'LOGOUT',
            'App\Models\User',
            auth()->id(),
            [
                'action_type' => 'authentication',
            ]
        );
    }

    /**
     * Log de importación
     */
    protected function logImport(string $type, int $recordsCount, array $additionalDetails = []): AuditLog
    {
        return $this->logActivity(
            "IMPORT_{$type}",
            null,
            null,
            array_merge([
                'action_type' => 'import',
                'import_type' => $type,
                'records_imported' => $recordsCount,
            ], $additionalDetails)
        );
    }

    /**
     * Log de exportación
     */
    protected function logExport(string $type, int $recordsCount, array $additionalDetails = []): AuditLog
    {
        return $this->logActivity(
            "EXPORT_{$type}",
            null,
            null,
            array_merge([
                'action_type' => 'export',
                'export_type' => $type,
                'records_exported' => $recordsCount,
            ], $additionalDetails)
        );
    }

    /**
     * Log de acción personalizada
     */
    protected function logCustomAction(string $actionName, array $details = []): AuditLog
    {
        return $this->logActivity(
            strtoupper($actionName),
            null,
            null,
            array_merge([
                'action_type' => 'custom',
            ], $details)
        );
    }
}
```

---

### **FASE 4: Implementar Middleware de Auditoría**
**Tiempo estimado:** 20 minutos

#### Objetivos:
- Crear middleware que registre automáticamente las peticiones importantes
- Filtrar rutas que deben ser auditadas
- Capturar errores y excepciones

#### Archivos a crear:
1. ✨ **CREAR:** `app/Http/Middleware/AuditMiddleware.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    /**
     * Rutas que NO deben ser auditadas
     */
    protected $excludedPaths = [
        'api/health',
        'api/ping',
        '_debugbar',
        'telescope',
    ];

    /**
     * Métodos HTTP que deben ser auditados
     */
    protected $auditedMethods = [
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Solo auditar si hay un usuario autenticado
        if (!auth()->check()) {
            return $response;
        }

        // Verificar si la ruta debe ser auditada
        if ($this->shouldAudit($request)) {
            $this->createAuditLog($request, $response);
        }

        return $response;
    }

    /**
     * Determinar si la petición debe ser auditada
     */
    protected function shouldAudit(Request $request): bool
    {
        // Verificar si es un método que debe ser auditado
        if (!in_array($request->method(), $this->auditedMethods)) {
            return false;
        }

        // Verificar si la ruta está excluida
        foreach ($this->excludedPaths as $excludedPath) {
            if (str_contains($request->path(), $excludedPath)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Crear el log de auditoría
     */
    protected function createAuditLog(Request $request, Response $response): void
    {
        try {
            $action = $this->determineAction($request);
            
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'endpoint' => $request->path(),
                'http_method' => $request->method(),
                'model_type' => null,
                'model_id' => null,
                'details' => [
                    'route_name' => $request->route()?->getName(),
                    'status_code' => $response->getStatusCode(),
                    'request_data' => $this->sanitizeRequestData($request),
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Exception $e) {
            // No lanzar excepción para no interrumpir el flujo
            logger()->error('Error creating audit log: ' . $e->getMessage());
        }
    }

    /**
     * Determinar la acción basada en la petición
     */
    protected function determineAction(Request $request): string
    {
        $method = $request->method();
        $path = $request->path();
        
        // Mapeo de métodos HTTP a acciones
        $actionMap = [
            'POST' => 'CREATE',
            'PUT' => 'UPDATE',
            'PATCH' => 'UPDATE',
            'DELETE' => 'DELETE',
        ];

        $action = $actionMap[$method] ?? 'ACTION';
        
        // Intentar extraer el recurso del path
        $segments = explode('/', $path);
        $resource = strtoupper($segments[0] ?? 'RESOURCE');
        
        return "{$action}_{$resource}";
    }

    /**
     * Sanitizar los datos de la petición (remover contraseñas, tokens, etc.)
     */
    protected function sanitizeRequestData(Request $request): array
    {
        $data = $request->except([
            'password',
            'password_confirmation',
            'token',
            '_token',
            '_method',
        ]);

        // Limitar tamaño de datos para evitar logs muy grandes
        return array_slice($data, 0, 50);
    }
}
```

2. 📝 **ACTUALIZAR:** `app/Http/Kernel.php` (o `bootstrap/app.php` en Laravel 11)

Para Laravel 11, actualizar `bootstrap/app.php`:

```php
// Dentro de withMiddleware():
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\AuditMiddleware::class,
    ]);
})
```

---

### **FASE 5: Actualizar Controladores con Auditoría**
**Tiempo estimado:** 45 minutos

#### Objetivos:
- Implementar el trait `LogsActivity` en todos los controladores importantes
- Agregar logs en puntos clave (CREATE, UPDATE, DELETE)
- Asegurar que se capturen todos los datos necesarios

#### Archivos a modificar:

##### 1. `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Traits\LogsActivity; // AGREGAR

class AuthenticatedSessionController extends Controller
{
    use LogsActivity; // AGREGAR

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // AGREGAR LOG DE LOGIN
        $this->logLogin($request->email);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // AGREGAR LOG DE LOGOUT ANTES DE CERRAR SESIÓN
        $this->logLogout();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
```

##### 2. `app/Http/Controllers/DocenteController.php`
```php
use App\Traits\LogsActivity;

class DocenteController extends Controller
{
    use LogsActivity;

    public function store(Request $request)
    {
        // ... validación y creación ...
        
        $docente = Docente::create($validated);
        
        // AGREGAR LOG
        $this->logCreate($docente, [
            'nombre_completo' => $docente->nombre . ' ' . $docente->apellido_paterno
        ]);
        
        return redirect()->route('docentes.index');
    }

    public function update(Request $request, Docente $docente)
    {
        // Capturar cambios antes de actualizar
        $changes = array_diff_assoc($request->only([...]), $docente->toArray());
        
        $docente->update($validated);
        
        // AGREGAR LOG
        $this->logUpdate($docente, $changes);
        
        return redirect()->route('docentes.index');
    }

    public function destroy(Docente $docente)
    {
        // AGREGAR LOG ANTES DE ELIMINAR
        $this->logDelete($docente);
        
        $docente->delete();
        
        return redirect()->route('docentes.index');
    }
}
```

##### Lista de controladores a actualizar:
- ✅ `DocenteController.php`
- ✅ `MateriaController.php`
- ✅ `HorarioController.php`
- ✅ `GrupoController.php`
- ✅ `AulaController.php`
- ✅ `SemestreController.php`
- ✅ `UserController.php`
- ✅ `RoleController.php`
- ✅ `AsistenciaController.php`
- ✅ `HorarioImportController.php`

---

### **FASE 6: Crear Controlador y Vistas de Bitácora**
**Tiempo estimado:** 60 minutos

#### Objetivos:
- Crear controlador para administrar la bitácora
- Crear vistas para visualizar, filtrar y buscar logs
- Implementar paginación y exportación

#### Archivos a crear:

##### 1. ✨ **CREAR:** `app/Http/Controllers/AuditLogController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    /**
     * Mostrar listado de logs
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filtro por usuario
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtro por acción
        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        // Filtro por modelo
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filtro por IP
        if ($request->filled('ip_address')) {
            $query->where('ip_address', $request->ip_address);
        }

        // Filtro por endpoint
        if ($request->filled('endpoint')) {
            $query->where('endpoint', 'like', '%' . $request->endpoint . '%');
        }

        // Filtro por rango de fechas
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $logs = $query->paginate(50)->withQueryString();

        // Obtener usuarios para el filtro
        $users = User::orderBy('name')->get();

        // Obtener tipos de modelos únicos
        $modelTypes = AuditLog::select('model_type')
            ->distinct()
            ->whereNotNull('model_type')
            ->pluck('model_type')
            ->map(function ($type) {
                return [
                    'value' => $type,
                    'label' => class_basename($type),
                ];
            });

        // Obtener acciones únicas
        $actions = AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('audit-logs.index', compact('logs', 'users', 'modelTypes', 'actions'));
    }

    /**
     * Mostrar detalles de un log específico
     */
    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');
        
        return view('audit-logs.show', compact('auditLog'));
    }

    /**
     * Obtener estadísticas de la bitácora
     */
    public function statistics()
    {
        $stats = [
            'total_logs' => AuditLog::count(),
            'total_users' => AuditLog::distinct('user_id')->count('user_id'),
            'logs_today' => AuditLog::whereDate('created_at', today())->count(),
            'logs_this_week' => AuditLog::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'logs_this_month' => AuditLog::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'top_actions' => AuditLog::select('action', DB::raw('count(*) as total'))
                ->groupBy('action')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get(),
            'top_users' => AuditLog::select('user_id', DB::raw('count(*) as total'))
                ->with('user')
                ->groupBy('user_id')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get(),
            'recent_logs' => AuditLog::with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
        ];

        return view('audit-logs.statistics', compact('stats'));
    }

    /**
     * Exportar logs a CSV
     */
    public function export(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Aplicar los mismos filtros que en index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $logs = $query->limit(5000)->get();

        $filename = 'audit_logs_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Encabezados
            fputcsv($file, [
                'ID',
                'Fecha/Hora',
                'Usuario',
                'Email',
                'Acción',
                'Endpoint',
                'Método HTTP',
                'IP',
                'Modelo',
                'ID Modelo',
            ]);

            // Datos
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user?->name ?? 'N/A',
                    $log->user?->email ?? 'N/A',
                    $log->action,
                    $log->endpoint,
                    $log->http_method,
                    $log->ip_address,
                    $log->model_type ? class_basename($log->model_type) : 'N/A',
                    $log->model_id ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Limpiar logs antiguos
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:7',
        ]);

        $date = now()->subDays($request->days);
        $count = AuditLog::where('created_at', '<', $date)->delete();

        return redirect()->route('audit-logs.index')
            ->with('success', "Se eliminaron {$count} registros anteriores a {$request->days} días.");
    }
}
```

##### 2. ✨ **CREAR:** `resources/views/audit-logs/index.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Bitácora del Sistema') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('audit-logs.statistics') }}" 
                   class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    📊 Estadísticas
                </a>
                <form action="{{ route('audit-logs.export') }}" method="GET" class="inline">
                    @foreach(request()->except('_token') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        📥 Exportar CSV
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('audit-logs.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Filtro por Usuario -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                            <select name="user_id" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Todos los usuarios</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro por Acción -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Acción</label>
                            <select name="action" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Todas las acciones</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                        {{ $action }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro por IP -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dirección IP</label>
                            <input type="text" name="ip_address" value="{{ request('ip_address') }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm" 
                                   placeholder="Ej: 192.168.1.1">
                        </div>

                        <!-- Filtro por Fecha Inicio -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Filtro por Fecha Fin -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hasta</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Filtro por Endpoint -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Endpoint</label>
                            <input type="text" name="endpoint" value="{{ request('endpoint') }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm" 
                                   placeholder="Ej: docentes">
                        </div>

                        <div class="md:col-span-3 flex gap-2">
                            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                                🔍 Filtrar
                            </button>
                            <a href="{{ route('audit-logs.index') }}" 
                               class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                                🔄 Limpiar Filtros
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Logs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha/Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Endpoint</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($logs as $log)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="font-medium text-gray-900">
                                                {{ $log->user?->name ?? 'Usuario Eliminado' }}
                                            </div>
                                            <div class="text-gray-500 text-xs">
                                                {{ $log->user?->email ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ str_contains($log->action, 'CREATE') ? 'bg-green-100 text-green-800' : '' }}
                                                {{ str_contains($log->action, 'UPDATE') ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ str_contains($log->action, 'DELETE') ? 'bg-red-100 text-red-800' : '' }}
                                                {{ str_contains($log->action, 'LOGIN') ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ str_contains($log->action, 'LOGOUT') ? 'bg-gray-100 text-gray-800' : '' }}
                                            ">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <code class="text-xs bg-gray-100 px-2 py-1 rounded">
                                                {{ $log->endpoint }}
                                            </code>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-1 text-xs font-semibold rounded
                                                {{ $log->http_method == 'POST' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $log->http_method == 'GET' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $log->http_method == 'PUT' || $log->http_method == 'PATCH' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $log->http_method == 'DELETE' ? 'bg-red-100 text-red-800' : '' }}
                                            ">
                                                {{ $log->http_method ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->ip_address }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('audit-logs.show', $log) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                Ver Detalles
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                            No se encontraron registros
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

##### 3. ✨ **CREAR:** `resources/views/audit-logs/show.blade.php`
##### 4. ✨ **CREAR:** `resources/views/audit-logs/statistics.blade.php`

---

### **FASE 7: Agregar Rutas**
**Tiempo estimado:** 5 minutos

#### Archivo a modificar:
📝 **ACTUALIZAR:** `routes/web.php`

```php
use App\Http\Controllers\AuditLogController;

Route::middleware(['auth'])->group(function () {
    // ... rutas existentes ...
    
    // Rutas de Bitácora (solo para admin)
    Route::middleware(['check.role:admin'])->group(function () {
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/statistics', [AuditLogController::class, 'statistics'])->name('audit-logs.statistics');
        Route::get('/audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
        Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
        Route::post('/audit-logs/cleanup', [AuditLogController::class, 'cleanup'])->name('audit-logs.cleanup');
    });
});
```

---

### **FASE 8: Agregar al Sistema de Módulos**
**Tiempo estimado:** 10 minutos

#### Objetivos:
- Agregar el módulo de bitácora al sistema de roles y permisos
- Actualizar el menú de navegación

#### Archivos a modificar:

📝 **ACTUALIZAR:** `app/Models/RoleModule.php`

```php
public static function getAvailableModules(): array
{
    return [
        // ... módulos existentes ...
        
        'bitacora' => [
            'name' => 'Bitácora',
            'icon' => 'clipboard-list',
            'description' => 'Registro de auditoría del sistema',
            'route' => 'audit-logs.index',
        ],
    ];
}
```

---

## 📊 RESUMEN DE ARCHIVOS

### Archivos a CREAR (8):
1. ✨ `database/migrations/2025_11_12_000001_add_endpoint_to_audit_logs_table.php`
2. ✨ `app/Traits/LogsActivity.php`
3. ✨ `app/Http/Middleware/AuditMiddleware.php`
4. ✨ `app/Http/Controllers/AuditLogController.php`
5. ✨ `resources/views/audit-logs/index.blade.php`
6. ✨ `resources/views/audit-logs/show.blade.php`
7. ✨ `resources/views/audit-logs/statistics.blade.php`
8. ✨ `resources/views/audit-logs/_partials/filter-form.blade.php`

### Archivos a ACTUALIZAR (15+):
1. 📝 `app/Models/AuditLog.php`
2. 📝 `bootstrap/app.php` (registrar middleware)
3. 📝 `routes/web.php`
4. 📝 `app/Models/RoleModule.php`
5. 📝 `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
6. 📝 `app/Http/Controllers/DocenteController.php`
7. 📝 `app/Http/Controllers/MateriaController.php`
8. 📝 `app/Http/Controllers/HorarioController.php`
9. 📝 `app/Http/Controllers/GrupoController.php`
10. 📝 `app/Http/Controllers/AulaController.php`
11. 📝 `app/Http/Controllers/SemestreController.php`
12. 📝 `app/Http/Controllers/UserController.php`
13. 📝 `app/Http/Controllers/RoleController.php`
14. 📝 `app/Http/Controllers/AsistenciaController.php`
15. 📝 `app/Http/Controllers/HorarioImportController.php`

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

### Fase 1: Base de Datos
- [ ] Crear migración para agregar campos `endpoint` y `http_method`
- [ ] Ejecutar `php artisan migrate`
- [ ] Verificar que la tabla tenga todos los campos necesarios

### Fase 2: Modelo
- [ ] Actualizar modelo `AuditLog`
- [ ] Agregar método estático `logAction()`
- [ ] Agregar scopes para filtrado
- [ ] Agregar accessors para formateo

### Fase 3: Trait
- [ ] Crear trait `LogsActivity`
- [ ] Implementar métodos helper (`logCreate`, `logUpdate`, `logDelete`, etc.)
- [ ] Probar trait en un controlador de prueba

### Fase 4: Middleware
- [ ] Crear `AuditMiddleware`
- [ ] Configurar rutas excluidas
- [ ] Registrar middleware en `bootstrap/app.php`
- [ ] Probar que se generen logs automáticamente

### Fase 5: Controladores
- [ ] Agregar trait `LogsActivity` en cada controlador
- [ ] Implementar logs en método `store()` (CREATE)
- [ ] Implementar logs en método `update()` (UPDATE)
- [ ] Implementar logs en método `destroy()` (DELETE)
- [ ] Implementar logs de autenticación (LOGIN/LOGOUT)
- [ ] Implementar logs de importación/exportación

### Fase 6: Vistas
- [ ] Crear vista `index.blade.php` con tabla de logs
- [ ] Crear vista `show.blade.php` con detalles
- [ ] Crear vista `statistics.blade.php` con estadísticas
- [ ] Implementar filtros funcionales
- [ ] Implementar paginación

### Fase 7: Rutas
- [ ] Agregar rutas en `web.php`
- [ ] Proteger con middleware `auth` y `check.role:admin`
- [ ] Probar acceso a cada ruta

### Fase 8: Módulos
- [ ] Agregar módulo 'bitacora' en `RoleModule`
- [ ] Actualizar menú de navegación
- [ ] Asignar módulo al rol Admin

### Fase 9: Testing
- [ ] Probar creación de logs en cada módulo
- [ ] Verificar que se capture IP correctamente
- [ ] Verificar que se capture usuario correctamente
- [ ] Verificar que se capture endpoint correctamente
- [ ] Probar filtros en la vista
- [ ] Probar exportación a CSV
- [ ] Probar vista de estadísticas

### Fase 10: Documentación
- [ ] Actualizar documentación del proyecto
- [ ] Crear guía de uso de la bitácora
- [ ] Documentar estructura de datos capturados

---

## 🎯 CRITERIOS DE ÉXITO

✅ **La implementación será exitosa cuando:**

1. Cada acción CRUD quede registrada en `audit_logs`
2. Se capture correctamente: IP, Usuario, Acción, Endpoint, Timestamp
3. Los administradores puedan ver, filtrar y exportar logs
4. No se generen errores en el proceso de registro
5. El rendimiento del sistema no se vea afectado significativamente
6. Los logs sean legibles y útiles para auditoría

---

## 📈 ESTIMACIÓN DE TIEMPO TOTAL

- **Fase 1:** 5 minutos
- **Fase 2:** 10 minutos
- **Fase 3:** 15 minutos
- **Fase 4:** 20 minutos
- **Fase 5:** 45 minutos
- **Fase 6:** 60 minutos
- **Fase 7:** 5 minutos
- **Fase 8:** 10 minutos
- **Testing y Ajustes:** 30 minutos

**TOTAL ESTIMADO: 3 horas 20 minutos**

---

## 🚀 ORDEN DE EJECUCIÓN RECOMENDADO

1. **Primero:** Fases 1 y 2 (Base de datos y Modelo)
2. **Segundo:** Fase 3 (Trait)
3. **Tercero:** Fase 4 (Middleware)
4. **Cuarto:** Fase 5 (Actualizar controladores críticos primero)
5. **Quinto:** Fases 6, 7 y 8 (Vistas y rutas)
6. **Sexto:** Testing completo

---

## 🔒 CONSIDERACIONES DE SEGURIDAD

1. **Sanitización de datos:** No guardar contraseñas ni tokens en los logs
2. **Acceso restringido:** Solo usuarios con rol Admin pueden ver la bitácora
3. **Límite de tamaño:** Limitar detalles guardados para evitar logs gigantes
4. **Limpieza periódica:** Implementar script para limpiar logs antiguos
5. **Protección de IPs:** Considerar encriptar IPs si es necesario por GDPR

---

## 📝 NOTAS IMPORTANTES

- El middleware captura automáticamente todas las peticiones POST, PUT, PATCH, DELETE
- Los controladores usan el trait para logs más específicos y detallados
- Ambos sistemas funcionan en conjunto para máxima cobertura
- Los logs NO deben interrumpir el flujo normal de la aplicación (try-catch)
- Se recomienda configurar un cronjob para limpieza automática de logs antiguos

---

## 🎨 PERSONALIZACIÓN OPCIONAL

Después de la implementación básica, se puede:

- [ ] Agregar notificaciones cuando ocurran acciones críticas
- [ ] Implementar gráficos interactivos en estadísticas
- [ ] Crear reportes automáticos diarios/semanales
- [ ] Implementar alertas por actividad sospechosa
- [ ] Agregar búsqueda avanzada con Elasticsearch

---

**FIN DEL PLAN DE IMPLEMENTACIÓN**

Este plan cubre todos los aspectos necesarios para implementar un sistema de bitácora robusto, profesional y escalable. ¿Procedemos con la implementación?
