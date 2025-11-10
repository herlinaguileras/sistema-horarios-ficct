<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Asistencia;
use App\Models\Horario;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstadisticaController extends Controller
{
    /**
     * Muestra estadísticas generales de todos los docentes
     */
    public function index()
    {
        $user = auth()->user();
        
        // Si el usuario es docente, redirigir a sus propias estadísticas
        if ($user->hasRole('docente') && $user->docente) {
            return redirect()->route('estadisticas.show', $user->docente->id);
        }
        
        // Solo admins pueden ver la lista de todos los docentes
        if (!$user->hasRole('admin')) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
        
        // Obtener todos los docentes con sus relaciones
        $docentes = Docente::with(['user', 'grupos.materia', 'grupos.horarios'])
            ->where('estado', 'Activo')
            ->get();

        $estadisticas = [];

        foreach ($docentes as $docente) {
            // Contar total de grupos asignados
            $totalGrupos = $docente->grupos->count();
            
            // Contar total de horarios (clases programadas)
            $totalHorarios = 0;
            foreach ($docente->grupos as $grupo) {
                $totalHorarios += $grupo->horarios->count();
            }

            // Obtener IDs de todos los horarios del docente
            $horarioIds = [];
            foreach ($docente->grupos as $grupo) {
                $horarioIds = array_merge($horarioIds, $grupo->horarios->pluck('id')->toArray());
            }

            // Contar asistencias registradas
            $totalAsistencias = Asistencia::whereIn('horario_id', $horarioIds)->count();
            
            // Contar asistencias del mes actual
            $asistenciasMesActual = Asistencia::whereIn('horario_id', $horarioIds)
                ->whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->year)
                ->count();

            // Calcular clases esperadas (desde inicio del semestre hasta hoy)
            // Asumiendo 4 clases por mes por horario como promedio
            $mesesTranscurridos = max(1, now()->diffInMonths(now()->startOfYear()));
            $clasesEsperadas = $totalHorarios * $mesesTranscurridos * 4; // 4 semanas por mes aprox
            
            // Calcular porcentaje de cumplimiento de registro
            $porcentajeCumplimiento = $clasesEsperadas > 0 
                ? round(($totalAsistencias / $clasesEsperadas) * 100, 2)
                : 0;
            
            // Limitar el porcentaje a 100% máximo
            $porcentajeCumplimiento = min($porcentajeCumplimiento, 100);

            // Calcular índice de constancia (asistencias último mes vs promedio)
            $asistenciasMesAnterior = Asistencia::whereIn('horario_id', $horarioIds)
                ->whereMonth('fecha', now()->subMonth()->month)
                ->whereYear('fecha', now()->subMonth()->year)
                ->count();

            $indiceConstancia = $asistenciasMesAnterior > 0
                ? round(($asistenciasMesActual / $asistenciasMesAnterior) * 100, 2)
                : ($asistenciasMesActual > 0 ? 100 : 0);

            // Calcular promedio de asistencias por horario
            $promedioAsistenciasPorHorario = $totalHorarios > 0
                ? round($totalAsistencias / $totalHorarios, 2)
                : 0;

            // Calcular días desde última asistencia
            $ultimaAsistencia = Asistencia::whereIn('horario_id', $horarioIds)
                ->orderBy('created_at', 'desc')
                ->first();
            
            $diasSinRegistro = $ultimaAsistencia 
                ? $ultimaAsistencia->created_at->diffInDays(now())
                : null;

            // Clasificar al docente según su cumplimiento
            $clasificacion = 'Sin datos';
            if ($porcentajeCumplimiento >= 90) {
                $clasificacion = 'Excelente';
            } elseif ($porcentajeCumplimiento >= 75) {
                $clasificacion = 'Bueno';
            } elseif ($porcentajeCumplimiento >= 60) {
                $clasificacion = 'Regular';
            } elseif ($porcentajeCumplimiento > 0) {
                $clasificacion = 'Necesita mejorar';
            }

            // Calcular frecuencia de registro (asistencias por semana)
            $semanasTranscurridas = max(1, now()->diffInWeeks(now()->startOfYear()));
            $frecuenciaSemanal = round($totalAsistencias / $semanasTranscurridas, 2);

            $estadisticas[] = [
                'docente' => $docente,
                'total_grupos' => $totalGrupos,
                'total_horarios' => $totalHorarios,
                'total_asistencias' => $totalAsistencias,
                'asistencias_mes_actual' => $asistenciasMesActual,
                'asistencias_mes_anterior' => $asistenciasMesAnterior,
                'clases_esperadas' => $clasesEsperadas,
                'porcentaje_cumplimiento' => $porcentajeCumplimiento,
                'indice_constancia' => $indiceConstancia,
                'promedio_asistencias_horario' => $promedioAsistenciasPorHorario,
                'frecuencia_semanal' => $frecuenciaSemanal,
                'dias_sin_registro' => $diasSinRegistro,
                'clasificacion' => $clasificacion,
                'ultima_asistencia' => $ultimaAsistencia,
            ];
        }

        // Ordenar por porcentaje de cumplimiento (descendente)
        usort($estadisticas, function($a, $b) {
            return $b['porcentaje_cumplimiento'] <=> $a['porcentaje_cumplimiento'];
        });

        return view('estadisticas.index', compact('estadisticas'));
    }

    /**
     * Muestra estadísticas detalladas de un docente específico
     */
    public function show(Docente $docente)
    {
        // Cargar relaciones necesarias
        $docente->load(['user', 'grupos.materia', 'grupos.semestre', 'grupos.horarios.aula', 'grupos.horarios.asistencias']);

        // Obtener todos los grupos del docente
        $grupos = $docente->grupos;

        $detallesGrupos = [];

        foreach ($grupos as $grupo) {
            $horarios = $grupo->horarios;
            
            foreach ($horarios as $horario) {
                // Obtener todas las asistencias de este horario ordenadas por fecha
                $asistencias = Asistencia::where('horario_id', $horario->id)
                    ->orderBy('fecha', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();

                // Contar estudiantes únicos que asistieron
                $estudiantesUnicos = $asistencias->pluck('estudiante_id')->unique()->count();

                // Agrupar asistencias por fecha para el historial
                $historialPorFecha = $asistencias->groupBy(function($asistencia) {
                    return \Carbon\Carbon::parse($asistencia->fecha)->format('Y-m-d');
                });

                // Crear historial detallado
                $historial = [];
                foreach ($historialPorFecha as $fecha => $asistenciasDia) {
                    $historial[] = [
                        'fecha' => $fecha,
                        'cantidad_estudiantes' => $asistenciasDia->count(),
                        'metodo_registro' => $asistenciasDia->first()->metodo_registro ?? 'manual',
                        'hora_registro' => $asistenciasDia->first()->created_at,
                        'asistencias' => $asistenciasDia
                    ];
                }

                $detallesGrupos[] = [
                    'grupo' => $grupo,
                    'horario' => $horario,
                    'total_asistencias' => $asistencias->count(),
                    'estudiantes_unicos' => $estudiantesUnicos,
                    'historial' => $historial,
                ];
            }
        }

        // Estadísticas generales del docente
        $totalAsistenciasRegistradas = Asistencia::whereIn('horario_id', 
            $grupos->flatMap->horarios->pluck('id')
        )->count();

        // Calcular promedio de asistencia
        $totalHorariosDocente = $grupos->flatMap->horarios->count();
        $totalClasesDictadas = 0;
        foreach ($detallesGrupos as $detalle) {
            $totalClasesDictadas += count($detalle['historial']);
        }
        
        // Calcular promedio basado en clases esperadas vs clases dictadas
        $mesesTranscurridos = max(1, now()->diffInMonths(now()->startOfYear()));
        $clasesEsperadasTotal = $totalHorariosDocente * $mesesTranscurridos * 4; // 4 semanas promedio por mes
        $promedioAsistenciaDocente = $clasesEsperadasTotal > 0 
            ? round(($totalClasesDictadas / $clasesEsperadasTotal) * 100, 2)
            : 0;

        // Asistencias por mes (últimos 6 meses)
        $asistenciasPorMes = [];
        for ($i = 5; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $mes = $fecha->format('Y-m');
            $nombreMes = $fecha->locale('es')->monthName;
            
            $cantidad = Asistencia::whereIn('horario_id', 
                $grupos->flatMap->horarios->pluck('id')
            )
            ->whereYear('fecha', $fecha->year)
            ->whereMonth('fecha', $fecha->month)
            ->count();

            $asistenciasPorMes[] = [
                'mes' => $nombreMes . ' ' . $fecha->year,
                'cantidad' => $cantidad,
            ];
        }

        return view('estadisticas.show', compact(
            'docente', 
            'detallesGrupos', 
            'totalAsistenciasRegistradas',
            'asistenciasPorMes',
            'promedioAsistenciaDocente',
            'totalClasesDictadas',
            'clasesEsperadasTotal'
        ));
    }
}
