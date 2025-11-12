<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Docente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule as ValidationRule;
use App\Traits\LogsActivity;

class UserController extends Controller
{
    use LogsActivity;
    /**
     * Muestra la lista de todos los usuarios.
     */
    public function index()
    {
        // Traemos usuarios con sus roles paginados
        $users = User::with('roles', 'docente')->orderBy('created_at', 'desc')->paginate(15);

        return view('users.index', compact('users'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     */
    public function store(Request $request)
    {
        // Validación - Solo un rol permitido
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,id'], // Cambio: solo un rol

            // Campos de docente (opcionales, solo si se selecciona rol docente)
            'codigo_docente' => ['nullable', 'string', 'max:255', 'unique:docentes'],
            'carnet_identidad' => ['nullable', 'string', 'max:255', 'unique:docentes'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'titulo' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request) {
            // Crear el usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => true, // Por defecto activo
            ]);

            // Asignar UN solo rol
            $user->roles()->sync([$request->role]);

            // Si tiene rol de docente Y se proporcionaron datos de docente, crear perfil
            $docenteRole = Role::where('name', 'docente')->first();

            if ($docenteRole && $request->role == $docenteRole->id) {
                // Validar que se proporcionaron los campos obligatorios de docente
                if ($request->filled('codigo_docente') && $request->filled('carnet_identidad')) {
                    $docente = $user->docente()->create([
                        'codigo_docente' => $request->codigo_docente,
                        'carnet_identidad' => $request->carnet_identidad,
                        'telefono' => $request->telefono,
                    ]);

                    // Crear título si se proporcionó
                    if ($request->filled('titulo')) {
                        $docente->titulos()->create([
                            'nombre' => $request->titulo,
                        ]);
                    }
                }
            }
        });

        // Registrar en bitácora
        $user = User::latest()->first();
        $this->logCreate($user, [
            'email' => $request->email,
            'role' => Role::find($request->role)->name ?? 'unknown',
        ]);

        return redirect()->route('users.index')
            ->with('status', '¡Usuario creado exitosamente!');
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles', 'docente.titulos');

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Actualiza un usuario existente en la base de datos.
     */
    public function update(Request $request, User $user)
    {
        // Verificar si el usuario es docente con perfil existente
        $isDocente = $user->hasRole('docente') && $user->docente;

        // Verificar si el usuario actual es admin (usando roles relationship)
        $currentUser = Auth::user();
        $isAdmin = $currentUser && $currentUser->roles()->where('name', 'admin')->exists();

        // Validación diferente según si es docente o no
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ];

        // Solo validar email si el usuario es admin
        if ($isAdmin) {
            $rules['email'] = ['required', 'string', 'email', 'max:255', ValidationRule::unique('users')->ignore($user->id)];
        }

        // Si NO es docente con perfil, permitir cambio de rol (solo un rol)
        if (!$isDocente) {
            $rules['role'] = ['required', 'exists:roles,id'];

            // Solo validar campos de docente si NO es docente existente
            $rules['codigo_docente'] = ['nullable', 'string', 'max:255', 'unique:docentes,codigo_docente'];
            $rules['carnet_identidad'] = ['nullable', 'string', 'max:255', 'unique:docentes,carnet_identidad'];
            $rules['telefono'] = ['nullable', 'string', 'max:20'];
            $rules['titulo'] = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($request, $user, $isDocente, $validated, $isAdmin) {
            // Actualizar datos básicos del usuario
            $updateData = [
                'name' => $validated['name'],
            ];

            // Solo actualizar email si el usuario es admin
            if ($isAdmin && isset($validated['email'])) {
                $updateData['email'] = $validated['email'];
            }

            $user->update($updateData);

            // Actualizar password solo si se proporcionó
            if (!empty($validated['password'])) {
                $user->update([
                    'password' => Hash::make($validated['password'])
                ]);
            }

            // Solo procesar cambio de rol si NO es docente con perfil
            if (!$isDocente && isset($validated['role'])) {
                $user->roles()->sync([$validated['role']]);

                // Si cambió a rol docente, crear perfil
                $docenteRole = Role::where('name', 'docente')->first();

                if ($docenteRole && $validated['role'] == $docenteRole->id) {
                    // Crear perfil de docente si no existe y se proporcionaron datos
                    if (!$user->docente && !empty($validated['codigo_docente']) && !empty($validated['carnet_identidad'])) {
                        $docente = $user->docente()->create([
                            'codigo_docente' => $validated['codigo_docente'],
                            'carnet_identidad' => $validated['carnet_identidad'],
                            'telefono' => $validated['telefono'] ?? null,
                            'estado' => 'Activo',
                        ]);

                        if (!empty($validated['titulo'])) {
                            $docente->titulos()->create([
                                'nombre' => $validated['titulo'],
                            ]);
                        }
                    }
                } else {
                    // Si cambió a otro rol y tenía perfil de docente, desactivar el perfil
                    if ($user->docente) {
                        $user->docente->update(['estado' => 'Inactivo']);
                    }
                }
            }
            // Si es docente existente, NO se modifica nada del perfil desde aquí
            // Los datos de docente solo se editan desde el módulo de Docentes
        });

        // Registrar en bitácora
        $this->logUpdate($user, [
            'name' => $request->name,
            'email' => $request->email,
        ], [
            'role' => Role::find($request->role ?? $user->roles->first()->id)->name ?? 'unknown',
        ]);

        return redirect()->route('users.index')
            ->with('status', '¡Usuario actualizado exitosamente!');
    }

    /**
     * Elimina un usuario de la base de datos.
     */
    public function destroy(User $user)
    {
        /** @var User|null $currentUser */
        $currentUser = Auth::user();

        // Prevenir que el usuario se elimine a sí mismo
        if ($currentUser && $user->id === $currentUser->id) {
            return back()->withErrors([
                'user' => 'No puedes eliminar tu propia cuenta.'
            ]);
        }

        // Prevenir eliminar si el docente tiene grupos/horarios asignados
        if ($user->docente && $user->docente->grupos()->count() > 0) {
            return back()->withErrors([
                'user' => 'No puedes eliminar este usuario porque tiene grupos asignados. Desactívalo en su lugar.'
            ]);
        }

        // Registrar en bitácora ANTES de eliminar
        $this->logDelete($user, [
            'email' => $user->email,
            'roles' => $user->roles->pluck('name')->toArray(),
        ]);

        $user->delete();

        return redirect()->route('users.index')
            ->with('status', '¡Usuario eliminado exitosamente!');
    }

    /**
     * Activa o desactiva un usuario.
     */
    public function toggleEstado(User $user)
    {
        /** @var User|null $currentUser */
        $currentUser = Auth::user();

        // Prevenir que el usuario se desactive a sí mismo
        if ($currentUser && $user->id === $currentUser->id) {
            return back()->withErrors([
                'user' => 'No puedes desactivar tu propia cuenta.'
            ]);
        }

        // Toggle del estado del usuario
        $user->update([
            'is_active' => !$user->is_active
        ]);

        // Si tiene perfil de docente, también actualizar su estado
        if ($user->docente) {
            $nuevoEstadoDocente = $user->is_active ? 'Activo' : 'Inactivo';
            $user->docente->update(['estado' => $nuevoEstadoDocente]);
        }

        $estadoTexto = $user->is_active ? 'activado' : 'desactivado';
        return back()->with('status', "Usuario {$estadoTexto} exitosamente.");
    }
}
