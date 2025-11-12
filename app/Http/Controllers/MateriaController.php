<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materia;
use App\Models\Carrera;
use Illuminate\Validation\Rule;
use App\Traits\LogsActivity;

class MateriaController extends Controller
{
    use LogsActivity;
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

    // Log de auditoría
    $this->logCreate($materia, [
        'carreras' => Carrera::whereIn('id', $validatedData['carreras'])->pluck('nombre')->toArray(),
        'nivel_semestre' => $validatedData['nivel_semestre'],
    ]);

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

        // Log de auditoría
        $this->logUpdate($materia, $validatedData, [
            'carreras_nuevas' => Carrera::whereIn('id', $validatedData['carreras'])->pluck('nombre')->toArray(),
        ]);

        // 4. REDIRIGIMOS A LA LISTA
        return redirect()->route('materias.index')->with('status', '¡Materia actualizada exitosamente!');
    }    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Materia $materia)
    {
        try {
            // Verificar si la materia tiene grupos asociados
            $gruposCount = $materia->grupos()->count();

            if ($gruposCount > 0) {
                return back()->withErrors([
                    'error' => "❌ No puedes eliminar esta materia porque tiene {$gruposCount} grupo(s) asociado(s). Debes eliminar primero los grupos."
                ]);
            }

            // Desvincular las carreras antes de eliminar
            $materia->carreras()->detach();

            // Log de auditoría ANTES de eliminar
            $this->logDelete($materia, [
                'sigla' => $materia->sigla,
                'nivel_semestre' => $materia->nivel_semestre,
                'carreras' => $materia->carreras->pluck('nombre')->toArray(),
            ]);

            // Eliminar la materia
            $materia->delete();

            return redirect()->route('materias.index')
                ->with('status', '✅ ¡Materia eliminada exitosamente!');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al eliminar la materia: ' . $e->getMessage()
            ]);
        }
    }
}
