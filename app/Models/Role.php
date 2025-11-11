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
     * Los módulos asignados a este rol
     */
    public function modules()
    {
        return $this->hasMany(RoleModule::class);
    }

    /**
     * Verificar si el rol tiene acceso a un módulo específico
     *
     * @param string $moduleName
     * @return bool
     */
    public function hasModule(string $moduleName): bool
    {
        return $this->modules()->where('module_name', $moduleName)->exists();
    }

    /**
     * Obtener nombres de todos los módulos del rol
     *
     * @return array
     */
    public function getModuleNames(): array
    {
        return $this->modules()->pluck('module_name')->toArray();
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
