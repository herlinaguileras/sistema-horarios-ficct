<?php

namespace App\Http\Controllers;
use App\Models\Grupo;
use App\Models\Aula;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Horario;
use App\Models\Semestre;
use Illuminate\Support\Facades\DB;
use App\Traits\LogsActivity;


class HorarioController extends Controller
{
    use LogsActivity;
    /**
     * Muestra la lista de todos los horarios.
     */
    public function index(Request $request)
    {
        $query = Horario::with(['grupo.semestre', 'grupo.materia', 'grupo.docente.user', 'aula']);

        // Aplicar filtros
        if ($request->filled('filtro_semestre_id')) {
            $query->whereHas('grupo', function ($q) use ($request) {
                $q->where('semestre_id', $request->filtro_semestre_id);
            });
        }

        if ($request->filled('filtro_docente_id')) {
            $query->whereHas('grupo', function ($q) use ($request) {
                $q->where('docente_id', $request->filtro_docente_id);
            });
        }

        if ($request->filled('filtro_materia_id')) {
            $query->whereHas('grupo', function ($q) use ($request) {
                $q->where('materia_id', $request->filtro_materia_id);
            });
        }

        if ($request->filled('filtro_grupo_id')) {
            $query->where('grupo_id', $request->filtro_grupo_id);
        }

        if ($request->filled('filtro_aula_id')) {
            $query->where('aula_id', $request->filtro_aula_id);
        }

        if ($request->filled('filtro_dia_semana')) {
            $query->where('dia_semana', $request->filtro_dia_semana);
        }

        $horarios = $query->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();

        // Cargar datos para los selectores de filtros
        $semestres = Semestre::orderBy('fecha_inicio', 'desc')->get();
        $docentes = \App\Models\Docente::with('user')->orderBy('id')->get();
        $materias = \App\Models\Materia::orderBy('nombre')->get();
        $grupos = Grupo::orderBy('nombre')->get();
        $aulas = Aula::orderBy('nombre')->get();

        $diasSemana = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo'
        ];

        return view('horarios.index', compact('horarios', 'semestres', 'docentes', 'materias', 'grupos', 'aulas', 'diasSemana'))
            ->with('filtros', $request->all());
    }

    /**
     * Muestra el formulario para crear un nuevo horario.
     */
    public function create()
    {
        $grupos = Grupo::with(['semestre', 'materia', 'docente.user'])->get();
        $aulas = Aula::all();

        return view('horarios.create', compact('grupos', 'aulas'));
    }

    /**
     * Almacena un nuevo horario en la base de datos, revisando conflictos.
     * Permite crear horarios para múltiples días a la vez.
     */
    public function store(Request $request)
    {
        // 1. VALIDAMOS LOS DATOS BÁSICOS
        $request->validate([
            'grupo_id' => ['required', 'exists:grupos,id'],
            'dias_semana' => ['required', 'array', 'min:1'],
            'dias_semana.*' => ['integer', 'between:1,7'],
            'aula_id' => ['required', 'exists:aulas,id'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
        ]);

        $grupo_id = $request->input('grupo_id');
        $dias = $request->input('dias_semana');
        $inicio = $request->input('hora_inicio');
        $fin = $request->input('hora_fin');
        $aula_id = $request->input('aula_id');

        $grupo = Grupo::findOrFail($grupo_id);
        $docente_id = $grupo->docente_id;

        // Función de solapamiento
        $funcionSolapamiento = function ($query) use ($inicio, $fin) {
            $query->where('hora_inicio', '<', $fin)
                  ->where('hora_fin', '>', $inicio);
        };

        // Array para almacenar conflictos
        $conflictos = [];

        // 2. VERIFICAR CONFLICTOS PARA CADA DÍA
        foreach ($dias as $dia) {
            // Conflicto 1: Aula
            $conflictoAula = Horario::where('dia_semana', $dia)
                ->where('aula_id', $aula_id)
                ->where($funcionSolapamiento)
                ->exists();

            if ($conflictoAula) {
                $nombreDia = $this->getNombreDia($dia);
                $conflictos[] = "Conflicto de Aula el {$nombreDia}: Esta aula ya está ocupada en ese horario.";
                continue;
            }

            // Conflicto 2: Docente
            $conflictoDocente = Horario::where('dia_semana', $dia)
                ->whereHas('grupo', function($query) use ($docente_id) {
                    $query->where('docente_id', $docente_id);
                })
                ->where($funcionSolapamiento)
                ->exists();

            if ($conflictoDocente) {
                $nombreDia = $this->getNombreDia($dia);
                $conflictos[] = "Conflicto de Docente el {$nombreDia}: El docente ya tiene otra clase asignada en ese horario.";
                continue;
            }

            // Conflicto 3: Grupo
            $conflictoGrupo = Horario::where('dia_semana', $dia)
                ->where('grupo_id', $grupo_id)
                ->where($funcionSolapamiento)
                ->exists();

            if ($conflictoGrupo) {
                $nombreDia = $this->getNombreDia($dia);
                $conflictos[] = "Conflicto de Grupo el {$nombreDia}: Este grupo ya tiene una clase asignada en ese horario.";
                continue;
            }
        }

        // Si hay conflictos, volver con errores
        if (!empty($conflictos)) {
            return back()->withErrors(['conflictos' => $conflictos])->withInput();
        }

        // 3. SI NO HAY CONFLICTOS, GUARDAMOS TODOS LOS HORARIOS
        DB::beginTransaction();
        try {
            $horariosCreados = [];
            foreach ($dias as $dia) {
                $horarioNuevo = Horario::create([
                    'grupo_id' => $grupo_id,
                    'aula_id' => $aula_id,
                    'dia_semana' => $dia,
                    'hora_inicio' => $inicio,
                    'hora_fin' => $fin,
                ]);
                $horariosCreados[] = $horarioNuevo->id;
            }

            // Log de auditoría
            $this->logActivity('CREATE_HORARIOS', 'App\Models\Horario', null, [
                'grupo' => $grupo->nombre,
                'materia' => $grupo->materia->nombre,
                'docente' => $grupo->docente->user->name,
                'aula' => Aula::find($aula_id)->nombre,
                'dias_creados' => count($dias),
                'horarios_ids' => $horariosCreados,
            ]);

            DB::commit();

            return redirect()
                ->route('horarios.index')
                ->with('status', '✅ ¡Horario(s) creado(s) exitosamente para ' . count($dias) . ' día(s)!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear los horarios: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Muestra el formulario para editar un horario existente.
     */
    public function edit(Horario $horario)
    {
        $grupos = Grupo::with(['semestre', 'materia', 'docente.user'])->get();
        $aulas = Aula::all();

        return view('horarios.edit', compact('horario', 'grupos', 'aulas'));
    }

    /**
     * Actualiza un horario existente en la base de datos.
     */
    public function update(Request $request, Horario $horario)
    {
        // Validación
        $request->validate([
            'grupo_id' => ['required', 'exists:grupos,id'],
            'dia_semana' => ['required', 'integer', 'between:1,7'],
            'aula_id' => ['required', 'exists:aulas,id'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
        ]);

        $grupo_id = $request->input('grupo_id');
        $dia = $request->input('dia_semana');
        $inicio = $request->input('hora_inicio');
        $fin = $request->input('hora_fin');
        $aula_id = $request->input('aula_id');

        $grupo = Grupo::findOrFail($grupo_id);
        $docente_id = $grupo->docente_id;

        // Función de solapamiento
        $funcionSolapamiento = function ($query) use ($inicio, $fin) {
            $query->where('hora_inicio', '<', $fin)
                  ->where('hora_fin', '>', $inicio);
        };

        // Verificar conflictos (excluyendo el horario actual)
        // Conflicto de Aula
        $conflictoAula = Horario::where('dia_semana', $dia)
            ->where('aula_id', $aula_id)
            ->where('id', '!=', $horario->id)
            ->where($funcionSolapamiento)
            ->exists();

        if ($conflictoAula) {
            return back()->withErrors([
                'aula_id' => '¡Conflicto de Aula! Esta aula ya está ocupada en ese día y hora.'
            ])->withInput();
        }

        // Conflicto de Docente
        $conflictoDocente = Horario::where('dia_semana', $dia)
            ->where('id', '!=', $horario->id)
            ->whereHas('grupo', function($query) use ($docente_id) {
                $query->where('docente_id', $docente_id);
            })
            ->where($funcionSolapamiento)
            ->exists();

        if ($conflictoDocente) {
            return back()->withErrors([
                'docente_id' => '¡Conflicto de Docente! El docente ya tiene otra clase asignada en ese día y hora.'
            ])->withInput();
        }

        // Conflicto de Grupo
        $conflictoGrupo = Horario::where('dia_semana', $dia)
            ->where('grupo_id', $grupo_id)
            ->where('id', '!=', $horario->id)
            ->where($funcionSolapamiento)
            ->exists();

        if ($conflictoGrupo) {
            return back()->withErrors([
                'grupo_id' => '¡Conflicto de Grupo! Este grupo ya tiene una clase asignada en ese día y hora.'
            ])->withInput();
        }

        // Actualizar
        $horario->update([
            'grupo_id' => $grupo_id,
            'aula_id' => $aula_id,
            'dia_semana' => $dia,
            'hora_inicio' => $inicio,
            'hora_fin' => $fin,
        ]);

        // Log de auditoría
        $this->logUpdate($horario, $request->all(), [
            'grupo' => $grupo->nombre,
            'aula' => Aula::find($aula_id)->nombre,
            'dia' => $this->getNombreDia($dia),
        ]);

        return redirect()
            ->route('horarios.index')
            ->with('status', '✅ ¡Horario actualizado exitosamente!');
    }

    /**
     * Elimina el horario especificado de la base de datos.
     */
    public function destroy(Horario $horario)
    {
        // Log de auditoría ANTES de eliminar
        $this->logDelete($horario, [
            'grupo' => $horario->grupo->nombre,
            'materia' => $horario->grupo->materia->nombre,
            'dia' => $this->getNombreDia($horario->dia_semana),
            'hora' => $horario->hora_inicio . ' - ' . $horario->hora_fin,
        ]);

        $horario->delete();

        return redirect()
            ->route('horarios.index')
            ->with('status', '✅ ¡Horario eliminado exitosamente!');
    }

    /**
     * Helper: Obtener nombre del día
     */
    private function getNombreDia($dia)
    {
        $dias = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo'
        ];
        return $dias[$dia] ?? 'Día ' . $dia;
    }
}
