<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            'logs_today' => AuditLog::whereDate('created_at', today())->count(),
            'active_users' => AuditLog::distinct('user_id')->count('user_id'),
            'deletions' => AuditLog::where('action', 'LIKE', '%DELETE%')->count(),
            'total_users' => AuditLog::distinct('user_id')->count('user_id'),
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
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get(),
            'top_endpoints' => AuditLog::select('endpoint', DB::raw('count(*) as total'))
                ->whereNotNull('endpoint')
                ->groupBy('endpoint')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get(),
            'top_ips' => AuditLog::select('ip_address', DB::raw('count(*) as total'))
                ->groupBy('ip_address')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get(),
            'activity_by_day' => AuditLog::selectRaw('DATE(created_at) as date, count(*) as count')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date', 'desc')
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
        // Log para depuración
        Log::info('Export method called', [
            'all_params' => $request->all(),
            'method' => $request->method(),
            'url' => $request->fullUrl()
        ]);

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
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

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
                    $log->endpoint ?? 'N/A',
                    $log->http_method ?? 'N/A',
                    $log->ip_address ?? 'N/A',
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
