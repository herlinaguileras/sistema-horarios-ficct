<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materia;
use App\Models\Carrera;
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
    // Obtener todas las materias con sus carreras
    $materias = Materia::with('carreras')->get();

    return view('materias.index', ['materias' => $materias]);
}

    /**
     * Show the form for creating a new resource.
     */

public function create()
{
    // Obtener todas las carreras activas
    $carreras = Carrera::where('activa', true)->get();
    
    return view('materias.create', compact('carreras'));
}

    /**
 * Store a newly created resource in storage.
 */
public function store(Request $request)
{
    // 1. VALIDAMOS LOS DATOS
    $validatedData = $request->validate([
        'nombre' => ['required', 'string', 'max:255'],
        'sigla' => ['required', 'string', 'max:255', 'unique:materias'],
        'nivel_semestre' => ['required', 'integer', 'min:1'],
        'carreras' => ['required', 'array', 'min:1'],
        'carreras.*' => ['required', 'integer', 'exists:carreras,id'],
    ]);

    // 2. CREAMOS LA MATERIA
    $materia = Materia::create([
        'nombre' => $validatedData['nombre'],
        'sigla' => $validatedData['sigla'],
        'nivel_semestre' => $validatedData['nivel_semestre'],
    ]);

    // 3. ASOCIAMOS LAS CARRERAS
    $materia->carreras()->attach($validatedData['carreras']);

    // 4. REDIRIGIMOS A LA LISTA
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
 * Muestra el formulario para editar la materia especificada.
 */
public function edit(Materia $materia)
{
    // Cargar las carreras de la materia y todas las carreras disponibles
    $materia->load('carreras');
    $carreras = Carrera::where('activa', true)->get();
    
    return view('materias.edit', compact('materia', 'carreras'));
}
    /**
     * Actualiza la materia especificada en la base de datos.
     */
    public function update(Request $request, Materia $materia)
    {
        // 1. VALIDAMOS LOS DATOS
        $validatedData = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'sigla' => [
                'required',
                'string',
                'max:255',
                Rule::unique('materias')->ignore($materia->id)
            ],
            'nivel_semestre' => ['required', 'integer', 'min:1'],
            'carreras' => ['required', 'array', 'min:1'],
            'carreras.*' => ['required', 'integer', 'exists:carreras,id'],
        ]);

        // 2. ACTUALIZAMOS LA MATERIA
        $materia->update([
            'nombre' => $validatedData['nombre'],
            'sigla' => $validatedData['sigla'],
            'nivel_semestre' => $validatedData['nivel_semestre'],
        ]);

        // 3. SINCRONIZAMOS LAS CARRERAS (elimina las antiguas y agrega las nuevas)
        $materia->carreras()->sync($validatedData['carreras']);

        // 4. REDIRIGIMOS A LA LISTA
        return redirect()->route('materias.index')->with('status', '¡Materia actualizada exitosamente!');
    }    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
