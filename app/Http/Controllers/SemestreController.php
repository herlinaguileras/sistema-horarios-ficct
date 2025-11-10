<?php

namespace App\Http\Controllers;

use App\Models\Semestre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SemestreController extends Controller
{
    /**
     * Muestra la lista de todos los semestres.
     */
    public function index()
    {
        $semestres = Semestre::orderBy('fecha_inicio', 'desc')->get();
        return view('semestres.index', compact('semestres'));
    }

    /**
     * Muestra el formulario para crear un nuevo semestre.
     */
    public function create()
    {
        $estados = Semestre::getEstados();
        return view('semestres.create', compact('estados'));
    }

    /**
     * Almacena un nuevo semestre en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:semestres,nombre'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after:fecha_inicio'],
            'estado' => ['required', Rule::in(Semestre::getEstados())],
        ], [
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'nombre.unique' => 'Ya existe un semestre con este nombre.',
        ]);

        DB::transaction(function () use ($request) {
            // Si se marca como activo, desactivar todos los demás
            if ($request->estado === Semestre::ESTADO_ACTIVO) {
                Semestre::where('estado', Semestre::ESTADO_ACTIVO)
                    ->update(['estado' => Semestre::ESTADO_TERMINADO]);
            }

            Semestre::create([
                'nombre' => $request->nombre,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'estado' => $request->estado,
            ]);
        });

        return redirect()->route('semestres.index')
            ->with('status', '✅ ¡Semestre creado exitosamente!');
    }

    /**
     * Muestra los detalles del semestre (redirige a editar).
     */
    public function show(Semestre $semestre)
    {
        return redirect()->route('semestres.edit', $semestre);
    }

    /**
     * Muestra el formulario para editar un semestre existente.
     */
    public function edit(Semestre $semestre)
    {
        $estados = Semestre::getEstados();
        return view('semestres.edit', compact('semestre', 'estados'));
    }

    /**
     * Actualiza el semestre especificado en la base de datos.
     */
    public function update(Request $request, Semestre $semestre)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('semestres', 'nombre')->ignore($semestre->id)],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after:fecha_inicio'],
            'estado' => ['required', Rule::in(Semestre::getEstados())],
        ], [
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'nombre.unique' => 'Ya existe un semestre con este nombre.',
        ]);

        DB::transaction(function () use ($request, $semestre) {
            // Si se marca como activo, cambiar todos los demás activos a terminado
            if ($request->estado === Semestre::ESTADO_ACTIVO) {
                Semestre::where('id', '!=', $semestre->id)
                    ->where('estado', Semestre::ESTADO_ACTIVO)
                    ->update(['estado' => Semestre::ESTADO_TERMINADO]);
            }

            $semestre->update([
                'nombre' => $request->nombre,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'estado' => $request->estado,
            ]);
        });

        return redirect()->route('semestres.index')
            ->with('status', '✅ ¡Semestre actualizado exitosamente!');
    }

    /**
     * Elimina el semestre especificado de la base de datos.
     */
    public function destroy(Semestre $semestre)
    {
        // Verificar que no sea el semestre activo
        if ($semestre->isActivo()) {
            return redirect()->route('semestres.index')
                ->withErrors(['error' => 'No se puede eliminar el semestre activo. Cámbialo a otro estado primero.']);
        }

        // Verificar si tiene grupos asociados
        if ($semestre->grupos()->count() > 0) {
            return redirect()->route('semestres.index')
                ->withErrors(['error' => 'No se puede eliminar este semestre porque tiene grupos asociados.']);
        }

        $semestre->delete();

        return redirect()->route('semestres.index')
            ->with('status', '✅ ¡Semestre eliminado exitosamente!');
    }

    /**
     * Cambia el estado de un semestre a Activo.
     */
    public function toggleActivo(Semestre $semestre)
    {
        DB::transaction(function () use ($semestre) {
            if (!$semestre->isActivo()) {
                // Si se va a activar, cambiar todos los demás activos a terminado
                Semestre::where('estado', Semestre::ESTADO_ACTIVO)
                    ->update(['estado' => Semestre::ESTADO_TERMINADO]);
                
                $semestre->update(['estado' => Semestre::ESTADO_ACTIVO]);
            }
        });

        return redirect()->route('semestres.index')
            ->with('status', '✅ Semestre activado exitosamente');
    }
}
