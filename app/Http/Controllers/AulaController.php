<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AulaController extends Controller
{
 /**
 * Display a listing of the resource.
 */
public function index()
{
    $aulas = Aula::all();
    return view('aulas.index', ['aulas' => $aulas]);
}

   /**
 * Show the form for creating a new resource.
 */
public function create()
{
    // Devolvemos la vista que contendrá el formulario
    return view('aulas.create');
}
   /**
 * Store a newly created resource in storage.
 */
/**
 * Store a newly created resource in storage.
 */
public function store(Request $request)
{
    // 1. VALIDAMOS LOS DATOS
    $validatedData = $request->validate([
        'nombre' => ['required', 'string', 'max:255', 'unique:aulas'],
        'piso' => ['required', 'integer'],
        'tipo' => ['required', 'string'],
        'capacidad' => ['nullable', 'integer', 'min:1'], // nullable = opcional
    ]);

    // 2. CREAMOS EL AULA
    Aula::create($validatedData);

    // 3. REDIRIGIMOS A LA LISTA
    return redirect()->route('aulas.index')->with('status', '¡Aula creada exitosamente!');
}
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

 /**
 * Muestra el formulario para editar el aula especificada.
 */
/**
 * Muestra el formulario para editar el aula especificada.
 */
public function edit(Aula $aula)
{
    // Laravel automáticamente encontrará el aula usando el ID de la URL

    // Devolvemos la vista 'edit' y le pasamos el aula
    return view('aulas.edit', ['aula' => $aula]);
}
  /**
     * Actualiza el aula especificada en la base de datos.
     */
    public function update(Request $request, Aula $aula)
    {
        // 1. VALIDAMOS LOS DATOS
        $validatedData = $request->validate([
            // Regla 'unique' especial: Le decimos que ignore el nombre
            // del aula que estamos editando actualmente.
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('aulas')->ignore($aula->id)
            ],
            'piso' => ['required', 'integer'],
            'tipo' => ['required', 'string'],
            'capacidad' => ['nullable', 'integer', 'min:1'], // nullable = opcional
        ]);

        // 2. ACTUALIZAMOS EL AULA
        $aula->update($validatedData);

        // 3. REDIRIGIMOS A LA LISTA
        return redirect()->route('aulas.index')->with('status', '¡Aula actualizada exitosamente!');
    }

   /**
 * Elimina el aula especificada de la base de datos.
 */
public function destroy(Aula $aula)
{
    // 1. ELIMINAMOS EL AULA
    $aula->delete();

    // 2. REDIRIGIMOS A LA LISTA
    return redirect()->route('aulas.index')->with('status', '¡Aula eliminada exitosamente!');
}
}
