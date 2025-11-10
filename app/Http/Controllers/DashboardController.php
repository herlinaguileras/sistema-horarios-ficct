<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Semestre;

use App\Models\Horario;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Asistencia;
use App\Models\Aula;
use Maatwebsite\Excel\Facades\Excel; // For the Excel facade
use App\Exports\HorarioSemanalExport; // Our export class
use Barryvdh\DomPDF\Facade\Pdf; // Or use PDF; if you have aliased it
// Make sure you also have Semestre, Horario use statements
use App\Exports\AsistenciaExport;
// Make sure 'use Maatwebsite\Excel\Facades\Excel;' and 'use App\Models\Semestre;' are also present


class DashboardController extends Controller
{
// app/Http/Controllers/DashboardController.php

// ... (use statements should include Request) ...

public function index(Request $request)
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */

        // --- Check User Role and Redirect to Appropriate Dashboard ---
        if ($user->hasRole('admin')) {
            return $this->adminDashboard($request);
        } elseif ($user->hasRole('docente')) {
            return $this->docenteDashboard($request);
        } else {
            // For custom roles, show dashboard based on permissions
            return $this->customRoleDashboard($request, $user);
        }
    }

    /**
     * Admin Dashboard Logic
     */
    private function adminDashboard(Request $request)
    {
        $activeTab = $request->input('tab', 'horarios');
        $semestreActivo = Semestre::where('estado', 'Activo')->first();
        $horariosPorDia = collect();
        $asistenciasAgrupadas = collect();
        $aulasDisponibles = null;
        $aulasOcupadas = null;
        $diasSemana = [
            1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
        ];

        if ($semestreActivo) {
            // Fetch Horario Semanal Data with Filters
            $horariosQuery = Horario::whereHas('grupo', function ($query) use ($semestreActivo) {
                    $query->where('semestre_id', $semestreActivo->id);
                })
                ->with(['grupo.materia', 'grupo.docente.user', 'aula']);

            // Apply filters for Horarios
            if ($request->filled('filtro_docente_id')) {
                $horariosQuery->whereHas('grupo', function ($query) use ($request) {
                    $query->where('docente_id', $request->filtro_docente_id);
                });
            }
            if ($request->filled('filtro_materia_id')) {
                $horariosQuery->whereHas('grupo', function ($query) use ($request) {
                    $query->where('materia_id', $request->filtro_materia_id);
                });
            }
            if ($request->filled('filtro_grupo_id')) {
                $horariosQuery->where('grupo_id', $request->filtro_grupo_id);
            }
            if ($request->filled('filtro_aula_id')) {
                $horariosQuery->where('aula_id', $request->filtro_aula_id);
            }
            if ($request->filled('filtro_dia_semana')) {
                $horariosQuery->where('dia_semana', $request->filtro_dia_semana);
            }

            $horarios = $horariosQuery->orderBy('dia_semana')->orderBy('hora_inicio')->get();
            $horariosPorDia = $horarios->groupBy('dia_semana');

            // Fetch Asistencia Data with Filters
            $asistenciasQuery = Asistencia::whereHas('horario.grupo', function ($query) use ($semestreActivo) {
                    $query->where('semestre_id', $semestreActivo->id);
                })
                ->with(['docente.user', 'horario.grupo.materia']);

            // Apply filters for Asistencias
            if ($request->filled('filtro_asist_docente_id')) {
                $asistenciasQuery->where('docente_id', $request->filtro_asist_docente_id);
            }
            if ($request->filled('filtro_asist_materia_id')) {
                $asistenciasQuery->whereHas('horario.grupo', function ($query) use ($request) {
                    $query->where('materia_id', $request->filtro_asist_materia_id);
                });
            }
            if ($request->filled('filtro_asist_grupo_id')) {
                $asistenciasQuery->whereHas('horario', function ($query) use ($request) {
                    $query->where('grupo_id', $request->filtro_asist_grupo_id);
                });
            }
            if ($request->filled('filtro_asist_estado')) {
                $asistenciasQuery->where('estado', $request->filtro_asist_estado);
            }
            if ($request->filled('filtro_asist_metodo')) {
                $asistenciasQuery->where('metodo_registro', $request->filtro_asist_metodo);
            }
            if ($request->filled('filtro_asist_fecha_inicio')) {
                $asistenciasQuery->where('fecha', '>=', $request->filtro_asist_fecha_inicio);
            }
            if ($request->filled('filtro_asist_fecha_fin')) {
                $asistenciasQuery->where('fecha', '<=', $request->filtro_asist_fecha_fin);
            }

            $asistencias = $asistenciasQuery
                ->orderByRaw('docente_id IS NULL, docente_id')
                ->orderBy('horario_id')
                ->orderBy('fecha', 'desc')
                ->orderBy('hora_registro', 'desc')
                ->get();

            $asistenciasAgrupadas = $asistencias->groupBy('docente_id')->map(function ($asistenciasDocente) {
                return $asistenciasDocente->groupBy(function ($asistencia) {
                    return $asistencia->horario->grupo_id ?? 'sin_grupo';
                });
            });

            // Fetch Aulas Disponibles Data (if tab is active)
            if ($activeTab === 'aulas' && $request->filled('check_date') && $request->filled('check_time')) {
                $fechaSeleccionada = Carbon::parse($request->input('check_date'));
                $horaSeleccionada = Carbon::parse($request->input('check_time'))->format('H:i:s');
                $numDiaSeleccionado = $fechaSeleccionada->dayOfWeekIso;

                $aulasOcupadasIds = Horario::where('dia_semana', $numDiaSeleccionado)
                    ->where('hora_inicio', '<=', $horaSeleccionada)->where('hora_fin', '>', $horaSeleccionada)
                    ->whereHas('grupo', fn($q) => $q->where('semestre_id', $semestreActivo->id))
                    ->pluck('aula_id')->unique()->toArray();
                $todasLasAulas = Aula::orderBy('nombre')->get();
                $aulasDisponibles = $todasLasAulas->whereNotIn('id', $aulasOcupadasIds);
                $aulasOcupadas = Horario::whereIn('aula_id', $aulasOcupadasIds)
                                    ->where('dia_semana', $numDiaSeleccionado)
                                    ->where('hora_inicio', '<=', $horaSeleccionada)->where('hora_fin', '>', $horaSeleccionada)
                                    ->whereHas('grupo', fn($q) => $q->where('semestre_id', $semestreActivo->id))
                                    ->with(['aula', 'grupo.materia', 'grupo.docente.user'])->get();
            }
        }

        // Cargar datos para los selectores de filtros
        $docentes = \App\Models\Docente::with('user')->orderBy('id')->get();
        $materias = \App\Models\Materia::orderBy('nombre')->get();
        $grupos = $semestreActivo ? \App\Models\Grupo::where('semestre_id', $semestreActivo->id)
            ->orderBy('nombre')->get() : collect();
        $aulas = \App\Models\Aula::orderBy('nombre')->get();

        return view('dashboards.admin', [
            'semestreActivo' => $semestreActivo,
            'horariosPorDia' => $horariosPorDia,
            'asistenciasAgrupadas' => $asistenciasAgrupadas,
            'aulasDisponibles' => $aulasDisponibles,
            'aulasOcupadas' => $aulasOcupadas,
            'diasSemana' => $diasSemana,
            'activeTab' => $activeTab,
            'docentes' => $docentes,
            'materias' => $materias,
            'grupos' => $grupos,
            'aulas' => $aulas,
            'filtros' => $request->all(),
        ]);
    }

    /**
     * Docente Dashboard Logic
     */
    private function docenteDashboard(Request $request)
    {
        $user = Auth::user();
        $docente = $user->docente;
        $semestreActivo = Semestre::where('estado', 'Activo')->first();
        $horariosDocente = collect();
        $horariosPorDia = collect();
        $totalAsistencias = 0;
        $activeTab = $request->input('tab', 'horario');

        $diasSemana = [
            1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
        ];

        if ($docente && $semestreActivo) {
            $horariosDocente = Horario::whereHas('grupo', function ($query) use ($docente, $semestreActivo) {
                    $query->where('docente_id', $docente->id)
                          ->where('semestre_id', $semestreActivo->id);
                })
                ->with(['grupo.materia', 'aula'])
                ->orderBy('dia_semana')->orderBy('hora_inicio')->get();

            $horariosPorDia = $horariosDocente->groupBy('dia_semana');

            // Calcular total de asistencias registradas
            $totalAsistencias = Asistencia::whereIn('horario_id', $horariosDocente->pluck('id'))->count();
        }

        return view('dashboards.docente', [
            'docente' => $docente,
            'semestreActivo' => $semestreActivo,
            'horariosDocente' => $horariosDocente,
            'horariosPorDia' => $horariosPorDia,
            'diasSemana' => $diasSemana,
            'totalAsistencias' => $totalAsistencias,
            'activeTab' => $activeTab
        ]);
    }

    /**
     * Custom Role Dashboard Logic (based on permissions)
     */
    private function customRoleDashboard(Request $request, $user)
    {
        // Obtener el primer rol del usuario (asumiendo que tiene uno principal)
        $role = $user->roles()->first();

        if (!$role) {
            return view('dashboard-default');
        }

        // Obtener todos los permisos del rol
        $permissions = $role->permissions;

        return view('dashboards.custom-role', [
            'role' => $role,
            'permissions' => $permissions,
            'user' => $user
        ]);
    }

/**
 * Handle the export of the weekly schedule to Excel.
 */
public function exportHorarioSemanal(Request $request)
{
    // 1. Find the active semester (same logic as index)
    $semestreActivo = Semestre::where('estado', 'Activo')->first();

    // 2. Check if an active semester exists
    if (!$semestreActivo) {
        // If not, redirect back with an error message
        return redirect()->route('dashboard')->withErrors(['export_error' => 'No hay un semestre activo para exportar.']);
    }

    // 3. Define the filename
    $fileName = 'horario_semanal_' . $semestreActivo->nombre . '.xlsx';

    // 4. Trigger the download using Laravel Excel
    // We pass the active semester's ID and filters to our export class
    return Excel::download(new HorarioSemanalExport($semestreActivo->id, $request->all()), $fileName);
}

/**
     * Handle the export of the weekly schedule to PDF.
     */
    public function exportHorarioSemanalPdf() // <-- CHECK THIS NAME CAREFULLY
    {
        // 1. Find the active semester
        $semestreActivo = Semestre::where('estado', 'Activo')->first();

        if (!$semestreActivo) {
            return redirect()->route('dashboard')->withErrors(['export_error' => 'No hay un semestre activo para exportar.']);
        }

        // 2. Fetch the data (same logic as index/Excel export)
        $horarios = Horario::whereHas('grupo', function ($query) use ($semestreActivo) {
                $query->where('semestre_id', $semestreActivo->id);
            })
            ->with(['grupo.materia', 'grupo.docente.user', 'aula'])
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();
        $horariosPorDia = $horarios->groupBy('dia_semana');

        $diasSemana = [
            1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
        ];

        // 3. Define the filename
        $fileName = 'horario_semanal_' . $semestreActivo->nombre . '.pdf';

        // 4. Load the PDF view with the data
        $pdf = Pdf::loadView('pdf.horario_semanal', [
            'semestreActivo' => $semestreActivo,
            'horariosPorDia' => $horariosPorDia,
            'diasSemana' => $diasSemana
        ]);

        // 5. Download the PDF
        return $pdf->download($fileName);
    }

    /**
 * Handle the export of attendance data to Excel.
 */
public function exportAsistencia(Request $request)
{
    $semestreActivo = Semestre::where('estado', 'Activo')->first();

    if (!$semestreActivo) {
        return redirect()->route('dashboard', ['tab' => 'asistencias']) // Redirect back to attendance tab
            ->withErrors(['export_error' => 'No hay un semestre activo para exportar.']);
    }

    $fileName = 'asistencia_' . $semestreActivo->nombre . '.xlsx';

    return Excel::download(new AsistenciaExport($semestreActivo->id, $request->all()), $fileName);
}

/**
 * Handle the export of attendance data to PDF.
 */
public function exportAsistenciaPdf()
{
    $semestreActivo = Semestre::where('estado', 'Activo')->first();

    if (!$semestreActivo) {
        return redirect()->route('dashboard', ['tab' => 'asistencias'])
            ->withErrors(['export_error' => 'No hay un semestre activo para exportar.']);
    }

    // Fetch and group attendance data (same logic as index)
    $asistencias = Asistencia::whereHas('horario.grupo', function ($query) use ($semestreActivo) {
            $query->where('semestre_id', $semestreActivo->id);
        })
        ->with(['docente.user', 'horario.grupo.materia'])
        ->orderBy('docente_id')->orderBy('horario_id')->orderBy('fecha', 'asc')->orderBy('hora_registro', 'asc')
        ->get();
    $asistenciasAgrupadas = $asistencias->groupBy(['docente_id', 'horario.grupo_id']);

    $fileName = 'asistencia_' . $semestreActivo->nombre . '.pdf';

    // Load the PDF view
    $pdf = Pdf::loadView('pdf.asistencia', [
        'semestreActivo' => $semestreActivo,
        'asistenciasAgrupadas' => $asistenciasAgrupadas
    ]);

    // Download the PDF
    return $pdf->download($fileName);
}


}
