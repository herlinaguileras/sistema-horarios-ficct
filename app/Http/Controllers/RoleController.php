<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Muestra la lista de todos los roles.
     */
    public function index(Request $request)
    {
        $query = Role::withCount(['users', 'permissions']);

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Ordenar por nivel descendente y luego por nombre
        $roles = $query->orderByLevel('desc')
                       ->orderBy('name')
                       ->get();

        return view('roles.index', compact('roles'));
    }

    /**
     * Muestra el formulario para crear un nuevo rol.
     */
    public function create()
    {
        // Cargar todos los permisos agrupados por módulo
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');
        
        return view('roles.create', compact('permissions'));
    }

    /**
     * Almacena un nuevo rol en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles',
                'regex:/^[a-z0-9_-]+$/'
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'level' => ['required', 'integer', 'min:1', 'max:100'],
            'status' => ['required', 'in:Activo,Inactivo'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ], [
            'name.regex' => 'El nombre solo puede contener letras minúsculas, números, guiones y guiones bajos.',
            'name.unique' => 'Ya existe un rol con este nombre.',
            'description.max' => 'La descripción no puede exceder 500 caracteres.',
        ]);

        try {
            DB::beginTransaction();

            // Crear el rol
            $role = Role::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'level' => $validated['level'],
                'status' => $validated['status'],
            ]);

            // Sincronizar permisos
            if (!empty($validated['permissions'])) {
                $role->permissions()->sync($validated['permissions']);
            }

            DB::commit();

            return redirect()->route('roles.index')
                ->with('status', '✅ ¡Rol creado exitosamente!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->withErrors(['error' => 'Error al crear el rol: ' . $e->getMessage()]);
        }
    }

    /**
     * Muestra el formulario para editar un rol existente.
     */
    public function edit(Role $role)
    {
        // Eager loading de relaciones
        $role->load(['users', 'permissions']);
        
        // Cargar todos los permisos agrupados por módulo
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');

        return view('roles.edit', compact('role', 'permissions'));
    }

    /**
     * Actualiza un rol existente en la base de datos.
     */
    public function update(Request $request, Role $role)
    {
        // Validar nombre de rol del sistema
        $nameRules = ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)];

        if (!$role->isSystemRole()) {
            $nameRules[] = 'regex:/^[a-z0-9_-]+$/';
        }

        $validated = $request->validate([
            'name' => $nameRules,
            'description' => ['nullable', 'string', 'max:500'],
            'level' => ['required', 'integer', 'min:1', 'max:100'],
            'status' => ['required', 'in:Activo,Inactivo'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ], [
            'name.regex' => 'El nombre solo puede contener letras minúsculas, números, guiones y guiones bajos.',
            'name.unique' => 'Ya existe un rol con este nombre.',
            'description.max' => 'La descripción no puede exceder 500 caracteres.',
        ]);

        try {
            DB::beginTransaction();

            // Actualizar el rol
            $role->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'level' => $validated['level'],
                'status' => $validated['status'],
            ]);

            // Sincronizar permisos
            $role->permissions()->sync($validated['permissions'] ?? []);

            DB::commit();

            return redirect()->route('roles.index')
                ->with('status', '✅ ¡Rol actualizado exitosamente!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->withErrors(['error' => 'Error al actualizar el rol: ' . $e->getMessage()]);
        }
    }

    /**
     * Elimina un rol de la base de datos.
     */
    public function destroy(Role $role)
    {
        // Prevenir eliminar roles del sistema
        if ($role->isSystemRole()) {
            return back()->withErrors([
                'error' => '❌ No puedes eliminar los roles del sistema (admin, docente).'
            ]);
        }

        // Verificar que no tenga usuarios asignados
        $usersCount = $role->users()->count();
        if ($usersCount > 0) {
            return back()->withErrors([
                'error' => "❌ No puedes eliminar este rol porque tiene {$usersCount} usuario(s) asignado(s)."
            ]);
        }

        try {
            // Desvincular permisos antes de eliminar
            $role->permissions()->detach();
            
            // Eliminar el rol
            $role->delete();

            return redirect()->route('roles.index')
                ->with('status', '✅ ¡Rol eliminado exitosamente!');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al eliminar el rol: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Cambia el estado de un rol (Activo/Inactivo)
     */
    public function toggleStatus(Role $role)
    {
        // Prevenir desactivar roles del sistema
        if ($role->isSystemRole() && $role->isActive()) {
            return back()->withErrors([
                'error' => '❌ No puedes desactivar los roles del sistema.'
            ]);
        }

        $newStatus = $role->isActive() ? 'Inactivo' : 'Activo';
        $role->update(['status' => $newStatus]);

        return back()->with('status', "✅ Rol {$newStatus} correctamente.");
    }
}
