<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
/**
     * Obtiene el perfil de docente asociado al usuario.
     */
    public function docente()
    {
        return $this->hasOne(Docente::class);
    }
    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Get the primary role of the user (first role).
     * Since users should only have one role, this returns the first one.
     */
    public function role()
    {
        return $this->roles()->first();
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        // Revisa si alguno de los roles del usuario tiene el nombre proporcionado
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Verificar si el usuario tiene acceso a un módulo específico
     *
     * @param string $moduleName
     * @return bool
     */
    public function hasModule(string $moduleName): bool
    {
        // Admin tiene acceso a todos los módulos
        if ($this->hasRole('admin')) {
            return true;
        }

        // Verificar a través de los roles del usuario
        return $this->roles()->whereHas('modules', function($query) use ($moduleName) {
            $query->where('module_name', $moduleName);
        })->exists();
    }

    /**
     * Obtener todos los módulos del usuario (a través de sus roles)
     *
     * @return array
     */
    public function getModules(): array
    {
        $modules = [];
        foreach ($this->roles as $role) {
            $modules = array_merge($modules, $role->getModuleNames());
        }
        return array_unique($modules);
    }
}
