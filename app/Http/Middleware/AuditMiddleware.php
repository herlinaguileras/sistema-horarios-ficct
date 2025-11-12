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
        'audit-logs',
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
