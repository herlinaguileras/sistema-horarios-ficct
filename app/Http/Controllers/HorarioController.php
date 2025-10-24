<?php

namespace App\Http\Controllers;
use App\Models\Grupo;
use App\Models\Aula;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Horario;
use App\Models\Semestre;


class HorarioController extends Controller
{
  /**
 * Muestra la lista de horarios para un grupo específico.
 */
public function index(Grupo $grupo)
{
    // 1. Laravel nos da el $grupo gracias a la ruta.

    // 2. Cargamos los horarios de ESE grupo y sus aulas
    //    (Usamos 'with' para ser eficientes)
    $horarios = $grupo->horarios()->with('aula')->get();

    // 3. Devolvemos la vista y le pasamos el grupo y sus horarios
    return view('horarios.index', [
        'grupo' => $grupo,
        'horarios' => $horarios,
    ]);
}
/**
 * Handle the export of the weekly schedule to PDF.
 */


   /**
 * Muestra el formulario para crear un nuevo horario para un grupo.
 */
public function create(Grupo $grupo)
{
    // 1. Laravel nos da el $grupo al que pertenece este horario.

    // 2. Obtenemos todas las aulas para el menú desplegable.
    $aulas = Aula::all();

    // 3. Devolvemos la vista y le pasamos el grupo y las aulas.
    return view('horarios.create', [
        'grupo' => $grupo,
        'aulas' => $aulas,
    ]);
}

/**
 * Almacena un nuevo horario en la base de datos, revisando conflictos.
 */
public function store(Request $request, Grupo $grupo)
{
    // 1. VALIDAMOS LOS DATOS BÁSICOS
    $request->validate([
        'dia_semana' => ['required', 'integer', 'between:1,7'], // Expects 1-7
        'aula_id' => ['required', 'exists:aulas,id'],
        'hora_inicio' => ['required', 'date_format:H:i'],
        'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
    ]);

    $dia = $request->input('dia_semana');
    $inicio = $request->input('hora_inicio');
    $fin = $request->input('hora_fin');
    $aula_id = $request->input('aula_id');
    $docente_id = $grupo->docente_id; // Obtenemos el ID del docente de este grupo

    // 2. LÓGICA ANTI-CONFLICTOS
    // Esta es la consulta clave para detectar un "cruce de horarios"
    // (HoraInicioNueva < HoraFinExistente) Y (HoraFinNueva > HoraInicioExistente)
    $funcionSolapamiento = function ($query) use ($inicio, $fin) {
        $query->where('hora_inicio', '<', $fin)
              ->where('hora_fin', '>', $inicio);
    };

    // Conflicto 1: Aula
    $conflictoAula = Horario::where('dia_semana', $dia)
        ->where('aula_id', $aula_id)
        ->where($funcionSolapamiento)
        ->exists(); // exists() es más rápido, solo devuelve true/false

    if ($conflictoAula) {
        // Si hay conflicto, volvemos atrás con un mensaje de error
        return back()->withErrors([
            'aula_id' => '¡Conflicto de Aula! Esta aula ya está ocupada en ese día y hora.'
        ])->withInput(); // withInput() mantiene los datos del formulario
    }

    // Conflicto 2: Docente
    // Buscamos si el docente ya tiene una clase en otro grupo a esa hora
    $conflictoDocente = Horario::where('dia_semana', $dia)
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

    // Conflicto 3: Grupo (Menos común, pero para ser rigurosos)
    // Revisa si este mismo grupo ya tiene una clase a esa hora
    $conflictoGrupo = $grupo->horarios()
        ->where('dia_semana', $dia)
        ->where($funcionSolapamiento)
        ->exists();

    if ($conflictoGrupo) {
         return back()->withErrors([
            'grupo_id' => '¡Conflicto de Grupo! Este grupo ya tiene una clase asignada en ese día y hora.'
        ])->withInput();
    }

    // 3. SI NO HAY CONFLICTOS, GUARDAMOS
    $grupo->horarios()->create([
        'aula_id' => $aula_id,
        'dia_semana' => $dia,
        'hora_inicio' => $inicio,
        'hora_fin' => $fin,
    ]);

    // 4. REDIRIGIMOS A LA LISTA
    return redirect()
        ->route('grupos.horarios.index', $grupo)
        ->with('status', '¡Horario añadido exitosamente!');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

/**
     * Elimina el horario especificado de la base de datos.
     */
    public function destroy(Horario $horario) // <-- ¿Dice Horario $horario aquí?
    {
        // Guardamos el grupo al que pertenecía para la redirección
        $grupo = $horario->grupo;

        // 1. ELIMINAMOS EL HORARIO
        $horario->delete();

        // 2. REDIRIGIMOS A LA LISTA DE HORARIOS DE ESE GRUPO
        return redirect()
            ->route('grupos.horarios.index', $grupo) // <-- ¿Usas $grupo aquí?
            ->with('status', '¡Horario eliminado exitosamente!');
    }

    /**
 * Display the QR code for a specific Horario.
 */
public function showQrCode(Horario $horario)
{
    // Maybe add authorization check: Is the logged-in user the assigned teacher?
    // We can add this later using Policies. For now, any logged-in user can see QR.

    // Load necessary data to display alongside the QR code (optional)
    $horario->load(['grupo.materia', 'aula', 'grupo.docente.user']);

    // Pass the horario object to the view
    return view('horarios.qr', ['horario' => $horario]);
}

}
