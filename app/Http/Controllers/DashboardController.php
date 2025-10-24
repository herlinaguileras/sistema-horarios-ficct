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
        $user = Auth::user(); // Get the logged-in user
        /** @var \App\Models\User $user */ // Help VS Code

        // --- Check User Role ---
        if ($user->hasRole('admin')) {
            // --- LOGIC FOR ADMIN DASHBOARD ---

            $activeTab = $request->input('tab', 'horarios');
            $semestreActivo = Semestre::where('estado', 'Activa')->first();
            $horariosPorDia = collect();
            $asistenciasAgrupadas = collect();
            $aulasDisponibles = null;
            $aulasOcupadas = null;
            $diasSemana = [
                1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
            ];

            if ($semestreActivo) {
                // Fetch Horario Semanal Data
                $horarios = Horario::whereHas('grupo', function ($query) use ($semestreActivo) {
                        $query->where('semestre_id', $semestreActivo->id);
                    })
                    ->with(['grupo.materia', 'grupo.docente.user', 'aula'])
                    ->orderBy('dia_semana')->orderBy('hora_inicio')->get();
                $horariosPorDia = $horarios->groupBy('dia_semana');

                // Fetch Asistencia Data
                $asistencias = Asistencia::whereHas('horario.grupo', function ($query) use ($semestreActivo) {
                        $query->where('semestre_id', $semestreActivo->id);
                    })
                    ->with(['docente.user', 'horario.grupo.materia'])
                    ->orderBy('docente_id')->orderBy('horario_id')->orderBy('fecha', 'desc')->orderBy('hora_registro', 'desc')->get();
                $asistenciasAgrupadas = $asistencias->groupBy(['docente_id', 'horario.grupo_id']);

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

            // Return the ADMIN dashboard view
            return view('dashboard', [
                'semestreActivo' => $semestreActivo,
                'horariosPorDia' => $horariosPorDia,
                'asistenciasAgrupadas' => $asistenciasAgrupadas,
                'aulasDisponibles' => $aulasDisponibles,
                'aulasOcupadas' => $aulasOcupadas,
                'diasSemana' => $diasSemana,
                'activeTab' => $activeTab
            ]);

        } elseif ($user->hasRole('docente')) {
            // --- LOGIC FOR DOCENTE DASHBOARD ---

            $docente = $user->docente;
            $semestreActivo = Semestre::where('estado', 'Activa')->first();
            $horariosDocente = collect();
            $diasSemana = [
                1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
            ];

            if ($docente && $semestreActivo) {
                $horariosDocente = Horario::whereHas('grupo', function ($query) use ($docente, $semestreActivo) {
                        $query->where('docente_id', $docente->id)
                              ->where('semestre_id', $semestreActivo->id);
                    })
                    ->with(['grupo.materia', 'aula'])
                    ->orderBy('dia_semana')->orderBy('hora_inicio')->get()
                    ->groupBy('dia_semana');
            }

            // Return the DOCENTE dashboard view
            return view('dashboard-docente', [
                'docente' => $docente,
                'semestreActivo' => $semestreActivo,
                'horariosDocente' => $horariosDocente,
                'diasSemana' => $diasSemana
            ]);

        } else {
            // --- LOGIC FOR OTHER ROLES ---
            return view('dashboard-default');
        }
    }

/**
 * Handle the export of the weekly schedule to Excel.
 */
public function exportHorarioSemanal()
{
    // 1. Find the active semester (same logic as index)
    $semestreActivo = Semestre::where('estado', 'Activa')->first();

    // 2. Check if an active semester exists
    if (!$semestreActivo) {
        // If not, redirect back with an error message
        return redirect()->route('dashboard')->withErrors(['export_error' => 'No hay un semestre activo para exportar.']);
    }

    // 3. Define the filename
    $fileName = 'horario_semanal_' . $semestreActivo->nombre . '.xlsx';

    // 4. Trigger the download using Laravel Excel
    // We pass the active semester's ID to our export class
    return Excel::download(new HorarioSemanalExport($semestreActivo->id), $fileName);
}

/**
     * Handle the export of the weekly schedule to PDF.
     */
    public function exportHorarioSemanalPdf() // <-- CHECK THIS NAME CAREFULLY
    {
        // 1. Find the active semester
        $semestreActivo = Semestre::where('estado', 'Activa')->first();

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
public function exportAsistencia()
{
    $semestreActivo = Semestre::where('estado', 'Activa')->first();

    if (!$semestreActivo) {
        return redirect()->route('dashboard', ['tab' => 'asistencias']) // Redirect back to attendance tab
            ->withErrors(['export_error' => 'No hay un semestre activo para exportar.']);
    }

    $fileName = 'asistencia_' . $semestreActivo->nombre . '.xlsx';

    return Excel::download(new AsistenciaExport($semestreActivo->id), $fileName);
}

/**
 * Handle the export of attendance data to PDF.
 */
public function exportAsistenciaPdf()
{
    $semestreActivo = Semestre::where('estado', 'Activa')->first();

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
