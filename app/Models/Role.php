<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'level',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'level' => 'integer',
    ];

    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    /**
     * Los permisos que tiene este rol
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    /**
     * Verificar si el rol tiene un permiso específico
     *
     * @param string $permissionName
     * @return bool
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions()->where('name', $permissionName)->exists();
    }

    /**
     * Verificar si es un rol del sistema que no se puede eliminar
     *
     * @return bool
     */
    public function isSystemRole(): bool
    {
        return in_array($this->name, ['admin', 'docente']);
    }

    /**
     * Verificar si el rol está activo
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 'Activo';
    }

    /**
     * Scope para roles activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Activo');
    }

    /**
     * Scope para ordenar por nivel
     */
    public function scopeOrderByLevel($query, $direction = 'desc')
    {
        return $query->orderBy('level', $direction);
    }
}
