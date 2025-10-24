<?php

namespace App\Http\Controllers;

use App\Models\Docente; // <-- 1. Importamos el modelo
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\Role;
use Illuminate\Support\Facades\Log; // <-- ADD THIS LINE
use Illuminate\Validation\Rule; // <-- ¡Esta es muy importante!

class DocenteController extends Controller
{
    /**
     * Muestra una lista de todos los docentes.
     */
    public function index()
    {
        // 2. Pedimos todos los docentes a la base de datos
        $docentes = Docente::with('user')->get();

        // 3. Devolvemos una vista y le pasamos la lista de docentes
        return view('docentes.index', [
            'docentes' => $docentes
        ]);
    }
    /**
 * Muestra el formulario para crear un nuevo docente.
 */
public function create()
{
    // Simplemente devolvemos la vista que contiene el formulario
    return view('docentes.create');
}

/**
 * Almacena un nuevo docente en la base de datos.
 */
/**
     * Almacena un nuevo docente en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. VALIDACIÓN
        $request->validate([
            // Reglas para 'users'
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

            // Reglas para 'docentes'
            'codigo_docente' => ['required', 'string', 'max:255', 'unique:docentes'],
            'carnet_identidad' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],

            // Regla para el primer título
            'titulo' => ['required', 'string', 'max:255'],
        ]);

        // 2. CREACIÓN (Usamos una transacción)
        DB::transaction(function () use ($request) {

            // 2a. Creamos el Usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // 2b. Creamos el Perfil de Docente
            $docente = $user->docente()->create([
                'codigo_docente' => $request->codigo_docente,
                'carnet_identidad' => $request->carnet_identidad,
                'telefono' => $request->telefono,
                // 'facultad' and 'estado' use defaults from migration
            ]);

            // 2c. Creamos su primer Título
            $docente->titulos()->create([
                'nombre' => $request->titulo
            ]);

            // --- Asignar Rol 'docente' ---
            $docenteRole = Role::where('name', 'docente')->first();
            if ($docenteRole) {
                $user->roles()->attach($docenteRole->id);
            } else {
                Log::error("Role 'docente' not found while creating user ID: " . $user->id);
            }
            // --- FIN Asignar Rol ---

        }); // End DB::transaction

        // 3. REDIRECCIÓN
        return redirect()->route('docentes.index')->with('status', '¡Docente creado exitosamente!');
    }

/**
     * Actualiza el docente especificado en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Docente  $docente
     */
    public function update(Request $request, Docente $docente)
    {
        // 1. VALIDACIÓN
        // Validamos los datos que vienen del formulario
        $request->validate([
            // Reglas para 'users'
            'name' => ['required', 'string', 'max:255'],
            // Regla 'unique' especial: Ignora al usuario actual,
            // de lo contrario fallará al guardar el mismo email.
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($docente->user_id)],

            // Reglas para 'docentes'
            'codigo_docente' => ['required', 'string', 'max:255', Rule::unique('docentes')->ignore($docente->id)],
            'carnet_identidad' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],

            // Regla para el título
            'titulo' => ['required', 'string', 'max:255'],

            // Regla para la CONTRASEÑA (¡Ahora es opcional!)
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. ACTUALIZACIÓN (Usamos una transacción)
        DB::transaction(function () use ($request, $docente) {

            // 2a. Actualizamos el Usuario
            $docente->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // 2b. LÓGICA CONDICIONAL DE CONTRASEÑA
            // Solo actualizamos la contraseña SI el campo no viene vacío.
            if ($request->filled('password')) {
                $docente->user->update([
                    'password' => Hash::make($request->password)
                ]);
            }

            // 2c. Actualizamos el Perfil de Docente
            $docente->update([
                'codigo_docente' => $request->codigo_docente,
                'carnet_identidad' => $request->carnet_identidad,
                'telefono' => $request->telefono,
            ]);

            // 2d. Actualizamos el primer Título
            // (Si no tiene, crea uno. Si tiene, actualiza el primero)
            $docente->titulos()->updateOrCreate(
                ['docente_id' => $docente->id], // Busca por esta condición
                ['nombre' => $request->titulo] // Actualiza/Crea con este dato
            );
        });

        // 3. REDIRECCIÓN
        // Redirigimos de vuelta a la lista con un mensaje de éxito.
        return redirect()->route('docentes.index')->with('status', '¡Docente actualizado exitosamente!');
    }


}
