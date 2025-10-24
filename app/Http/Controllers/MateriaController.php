<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materia;
use Illuminate\Validation\Rule;

class MateriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   /**
 * Display a listing of the resource.
 */
public function index()
{
    // 1. Obtenemos todas las materias
    $materias = Materia::all();

    // 2. Devolvemos una vista y le pasamos las materias
    return view('materias.index', ['materias' => $materias]);
}

    /**
     * Show the form for creating a new resource.
     */

public function create()
{
    // Simplemente devolvemos la vista que contendrá el formulario
    return view('materias.create');
}

    /**
 * Store a newly created resource in storage.
 */
public function store(Request $request)
{
    // 1. VALIDAMOS LOS DATOS
    // (nombre, codigo_materia, nivel_semestre, carrera)
   // ...
$validatedData = $request->validate([
    'nombre' => ['required', 'string', 'max:255'],
    'sigla' => ['required', 'string', 'max:255', 'unique:materias'], // <-- CAMBIO AQUÍ
    'nivel_semestre' => ['required', 'integer', 'min:1'],
    'carrera' => ['required', 'string'],
]);
// ...

    // 2. CREAMOS LA MATERIA
    // Gracias al $fillable, podemos hacer esto de forma segura
    Materia::create($validatedData);

    // 3. REDIRIGIMOS A LA LISTA
    // Volvemos a la lista de materias con un mensaje de éxito
    return redirect()->route('materias.index')->with('status', '¡Materia creada exitosamente!');
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
   /**
/**
 * Muestra el formulario para editar la materia especificada.
 */
public function edit(Materia $materia)
{
    // Laravel automáticamente encontrará la materia usando el ID de la URL

    // Devolvemos la vista 'edit' y le pasamos la materia
    return view('materias.edit', ['materia' => $materia]);
}
/**
     * Actualiza la materia especificada en la base de datos.
     */
    public function update(Request $request, Materia $materia)
    {
        // 1. VALIDAMOS LOS DATOS
        $validatedData = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],

            // Regla 'unique' especial: Le decimos que ignore la sigla
            // de la materia que estamos editando actualmente.
            'sigla' => [
                'required',
                'string',
                'max:255',
                Rule::unique('materias')->ignore($materia->id)
            ],

            'nivel_semestre' => ['required', 'integer', 'min:1'],
            'carrera' => ['required', 'string'],
        ]);

        // 2. ACTUALIZAMOS LA MATERIA
        $materia->update($validatedData);

        // 3. REDIRIGIMOS A LA LISTA
        return redirect()->route('materias.index')->with('status', '¡Materia actualizada exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
