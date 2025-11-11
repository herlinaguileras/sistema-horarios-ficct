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
    // Calcular el próximo código docente
    $ultimoDocente = Docente::orderBy('codigo_docente', 'desc')->first();
    $proximoCodigo = $ultimoDocente ? ((int)$ultimoDocente->codigo_docente + 1) : 100;
    
    // Devolvemos la vista con el próximo código
    return view('docentes.create', ['proximoCodigo' => $proximoCodigo]);
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
            'carnet_identidad' => ['required', 'string', 'max:255', 'unique:docentes,carnet_identidad'],
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

            // 2b. Auto-generar código docente
            // Obtener el último código docente y sumar 1, o empezar desde 100
            $ultimoDocente = Docente::orderBy('codigo_docente', 'desc')->first();
            $nuevoCodigo = $ultimoDocente ? ((int)$ultimoDocente->codigo_docente + 1) : 100;

            // 2c. Creamos el Perfil de Docente
            $docente = $user->docente()->create([
                'codigo_docente' => (string)$nuevoCodigo,
                'carnet_identidad' => $request->carnet_identidad,
                'telefono' => $request->telefono,
                'estado' => 'Activo', // Estado activo por defecto
                // 'facultad' uses default from migration
            ]);

            // 2d. Creamos su primer Título
            $docente->titulos()->create([
                'nombre' => $request->titulo
            ]);

            // 2e. Asignar Rol 'docente' automáticamente
            $docenteRole = Role::where('name', 'docente')->first();
            if ($docenteRole) {
                $user->roles()->attach($docenteRole->id);
            } else {
                Log::error("Role 'docente' not found while creating user ID: " . $user->id);
            }

        }); // End DB::transaction

        // 3. REDIRECCIÓN
        return redirect()->route('docentes.index')->with('status', '✅ ¡Docente creado exitosamente con código ' . ((int)(Docente::orderBy('codigo_docente', 'desc')->first()->codigo_docente ?? 100)) . '!');
    }

    /**
     * Muestra el formulario para editar un docente existente.
     */
    public function edit(Docente $docente)
    {
        // Cargamos las relaciones necesarias
        $docente->load(['user', 'titulos']);
        
        return view('docentes.edit', compact('docente'));
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
        
        // Reglas base
        $rules = [
            // Reglas para 'users'
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($docente->user_id)],
            'telefono' => ['nullable', 'string', 'max:20'],
            'titulo' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ];
        
        // Solo validar unicidad del carnet si ha cambiado
        if ($request->carnet_identidad !== $docente->carnet_identidad) {
            $rules['carnet_identidad'] = ['required', 'string', 'max:255', 'unique:docentes,carnet_identidad'];
        } else {
            $rules['carnet_identidad'] = ['required', 'string', 'max:255'];
        }
        
        $request->validate($rules);

        // 2. ACTUALIZACIÓN (Usamos una transacción)
        DB::transaction(function () use ($request, $docente) {

            // 2a. Actualizamos el nombre y email del Usuario
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

            // 2c. Actualizamos el Perfil de Docente (sin tocar codigo_docente)
            $docente->update([
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
        return redirect()->route('docentes.index')->with('status', '✅ ¡Docente actualizado exitosamente!');
    }

    /**
     * Elimina el docente especificado de la base de datos.
     */
    public function destroy(Docente $docente)
    {
        // Verificar si el docente tiene grupos asignados
        $gruposCount = $docente->grupos()->count();
        
        if ($gruposCount > 0) {
            return redirect()->route('docentes.index')
                ->with('error', "❌ No se puede eliminar el docente porque tiene {$gruposCount} grupo(s) asignado(s). Por favor, reasigna o elimina los grupos primero.");
        }
        
        DB::transaction(function () use ($docente) {
            // Guardamos el nombre para el mensaje
            $nombre = $docente->user->name;
            
            // 1. Eliminamos los títulos asociados
            $docente->titulos()->delete();
            
            // 2. Eliminamos los horarios de los grupos (si hubiera alguno sin eliminar)
            // Esto es por precaución aunque no debería haber grupos a esta altura
            foreach ($docente->grupos as $grupo) {
                $grupo->horarios()->delete();
                $grupo->delete();
            }
            
            // 3. Desvinculamos el rol de docente del usuario
            $docenteRole = Role::where('name', 'docente')->first();
            if ($docenteRole) {
                $docente->user->roles()->detach($docenteRole->id);
            }
            
            // 4. Eliminamos el perfil de docente
            $docente->delete();
            
            // 5. Eliminamos el usuario asociado
            $docente->user->delete();
        });
        
        return redirect()->route('docentes.index')->with('status', '✅ ¡Docente eliminado exitosamente!');
    }


}
