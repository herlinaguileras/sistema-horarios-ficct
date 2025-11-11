<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleModule extends Model
{
    protected $fillable = [
        'role_id',
        'module_name',
    ];

    /**
     * Relación con el rol
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Módulos disponibles en el sistema
     */
    public static function availableModules(): array
    {
        return [
            'usuarios' => [
                'name' => 'Usuarios',
                'icon' => 'users',
                'color' => 'blue',
                'route' => 'users.index',
                'description' => 'Gestión de usuarios del sistema',
            ],
            'roles' => [
                'name' => 'Roles',
                'icon' => 'shield',
                'color' => 'purple',
                'route' => 'roles.index',
                'description' => 'Gestión de roles y permisos',
            ],
            'docentes' => [
                'name' => 'Docentes',
                'icon' => 'user',
                'color' => 'green',
                'route' => 'docentes.index',
                'description' => 'Gestión de profesores',
            ],
            'materias' => [
                'name' => 'Materias',
                'icon' => 'book',
                'color' => 'yellow',
                'route' => 'materias.index',
                'description' => 'Gestión de asignaturas',
            ],
            'aulas' => [
                'name' => 'Aulas',
                'icon' => 'building',
                'color' => 'red',
                'route' => 'aulas.index',
                'description' => 'Gestión de salones y espacios',
            ],
            'grupos' => [
                'name' => 'Grupos',
                'icon' => 'group',
                'color' => 'pink',
                'route' => 'grupos.index',
                'description' => 'Gestión de grupos de estudiantes',
            ],
            'semestres' => [
                'name' => 'Semestres',
                'icon' => 'calendar',
                'color' => 'teal',
                'route' => 'semestres.index',
                'description' => 'Gestión de períodos académicos',
            ],
            'horarios' => [
                'name' => 'Horarios y Asistencias',
                'icon' => 'clock',
                'color' => 'indigo',
                'route' => 'horarios.index',
                'description' => 'Gestión de horarios y registro de asistencias',
            ],
            'importacion' => [
                'name' => 'Importar Horarios',
                'icon' => 'upload',
                'color' => 'cyan',
                'route' => 'importacion.index',
                'description' => 'Importación masiva de horarios desde Excel/CSV',
            ],
            'estadisticas' => [
                'name' => 'Estadísticas',
                'icon' => 'chart',
                'color' => 'orange',
                'route' => 'estadisticas.index',
                'description' => 'Ver estadísticas y reportes',
            ],
        ];
    }
}
