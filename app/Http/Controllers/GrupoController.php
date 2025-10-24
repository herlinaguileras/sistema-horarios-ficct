<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Semestre;
use App\Models\Materia;
use App\Models\Docente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class GrupoController extends Controller
{
   /**
 * Display a listing of the resource.
 */
public function index()
{
    // Optimizamos la consulta: Traemos el grupo Y sus relaciones
    $grupos = Grupo::with(['semestre', 'materia', 'docente.user'])->get();

    return view('grupos.index', ['grupos' => $grupos]);
}

 /**
 * Muestra el formulario para crear un nuevo grupo (carga horaria).
 */
public function create()
{
    // 1. Obtenemos los datos para los menús desplegables
    $semestres = Semestre::all();
    $materias = Materia::all();
    // Para docentes, optimizamos para traer también su 'user' (nombre)
    $docentes = Docente::with('user')->get();

    // 2. Devolvemos la vista y le pasamos las 3 listas
    return view('grupos.create', [
        'semestres' => $semestres,
        'materias' => $materias,
        'docentes' => $docentes,
    ]);
}

   /**
 * Store a newly created resource in storage.
 */
public function store(Request $request)
{
    // 1. VALIDAMOS LOS DATOS
    // Nos aseguramos de que los IDs existan en sus tablas
    $validatedData = $request->validate([
        'semestre_id' => ['required', 'exists:semestres,id'],
        'materia_id' => ['required', 'exists:materias,id'],
        'docente_id' => ['required', 'exists:docentes,id'],
        'nombre' => ['required', 'string', 'max:10'], // Ej: "SA", "SB"
    ]);

    // 2. CREAMOS EL GRUPO (CARGA HORARIA)
    Grupo::create($validatedData);

    // 3. REDIRIGIMOS A LA LISTA
    return redirect()->route('grupos.index')->with('status', '¡Grupo (Carga Horaria) creado exitosamente!');
}
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

 /**
 * Muestra el formulario para editar el grupo especificado.
 */
public function edit(Grupo $grupo)
{
    // 1. Laravel ya nos da el $grupo que queremos editar.

    // 2. Obtenemos los datos para los menús desplegables
    $semestres = Semestre::all();
    $materias = Materia::all();
    $docentes = Docente::with('user')->get();

    // 3. Devolvemos la vista y le pasamos todo
    return view('grupos.edit', [
        'grupo' => $grupo,
        'semestres' => $semestres,
        'materias' => $materias,
        'docentes' => $docentes,
    ]);
}
  /**
     * Actualiza el grupo especificado en la base de datos.
     */
    public function update(Request $request, Grupo $grupo)
    {
        // 1. VALIDAMOS LOS DATOS
        $validatedData = $request->validate([
            'semestre_id' => ['required', 'exists:semestres,id'],
            'materia_id' => ['required', 'exists:materias,id'],
            'docente_id' => ['required', 'exists:docentes,id'],
            'nombre' => ['required', 'string', 'max:10'],
        ]);

        // 2. ACTUALIZAMOS EL GRUPO
        $grupo->update($validatedData);

        // 3. REDIRIGIMOS A LA LISTA
        return redirect()->route('grupos.index')->with('status', '¡Grupo (Carga Horaria) actualizado exitosamente!');
    }

   /**
 * Elimina el grupo especificado de la base de datos.
 */
public function destroy(Grupo $grupo)
{
    // 1. ELIMINAMOS EL GRUPO
    $grupo->delete();

    // 2. REDIRIGIMOS A LA LISTA
    return redirect()->route('grupos.index')->with('status', '¡Grupo (Carga Horaria) eliminado exitosamente!');
}
}
