<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        // Si el usuario no está autenticado, redirigir al login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Si es admin, permitir acceso total
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Verificar si el usuario tiene el módulo específico
        if (!$user->hasModule($module)) {
            abort(403, 'No tienes acceso a este módulo.');
        }

        return $next($request);
    }
}
