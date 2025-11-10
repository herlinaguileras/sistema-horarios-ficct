<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Horario;
use App\Models\Asistencia;
use Carbon\Carbon;

class DocenteDashboardController extends Controller
{
    /**
     * Muestra el horario semanal del docente.
     */
    public function horarioSemanal()
    {
        $user = Auth::user();
        $docente = $user->docente;

        if (!$docente) {
            abort(403, 'No tienes un perfil de docente asignado.');
        }

        // Obtener horarios del docente con relaciones necesarias
        $horariosDocente = Horario::whereHas('grupo', function ($query) use ($docente) {
            $query->where('docente_id', $docente->id);
        })
        ->with(['grupo.materia', 'grupo.semestre', 'aula'])
        ->orderBy('dia_semana')
        ->orderBy('hora_inicio')
        ->get();

        $diasSemana = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo'
        ];

        return view('docente.horario-semanal', compact('docente', 'horariosDocente', 'diasSemana'));
    }

    /**
     * Muestra la página para marcar asistencia.
     */
    public function marcarAsistencia()
    {
        $user = Auth::user();
        $docente = $user->docente;

        if (!$docente) {
            abort(403, 'No tienes un perfil de docente asignado.');
        }

        // Obtener horarios del docente para el semestre activo
        $horariosDocente = Horario::whereHas('grupo', function ($query) use ($docente) {
            $query->where('docente_id', $docente->id)
                  ->whereHas('semestre', function ($q) {
                      $q->where('estado', 'Activo');
                  });
        })
        ->with(['grupo.materia', 'grupo.semestre', 'aula'])
        ->orderBy('dia_semana')
        ->orderBy('hora_inicio')
        ->get();

        return view('docente.marcar-asistencia', compact('docente', 'horariosDocente'));
    }

    /**
     * Muestra las estadísticas del docente.
     */
    public function misEstadisticas()
    {
        $user = Auth::user();
        $docente = $user->docente;

        if (!$docente) {
            abort(403, 'No tienes un perfil de docente asignado.');
        }

        // Redirigir a la vista de estadísticas existente
        return redirect()->route('estadisticas.show', $docente->id);
    }
}
