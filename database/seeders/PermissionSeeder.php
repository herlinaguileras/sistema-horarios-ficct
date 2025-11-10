<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Usuarios
            ['name' => 'ver_usuarios', 'description' => 'Ver lista de usuarios', 'module' => 'Usuarios'],
            ['name' => 'crear_usuarios', 'description' => 'Crear nuevos usuarios', 'module' => 'Usuarios'],
            ['name' => 'editar_usuarios', 'description' => 'Editar usuarios existentes', 'module' => 'Usuarios'],
            ['name' => 'eliminar_usuarios', 'description' => 'Eliminar usuarios', 'module' => 'Usuarios'],

            // Roles
            ['name' => 'ver_roles', 'description' => 'Ver lista de roles', 'module' => 'Roles'],
            ['name' => 'crear_roles', 'description' => 'Crear nuevos roles', 'module' => 'Roles'],
            ['name' => 'editar_roles', 'description' => 'Editar roles existentes', 'module' => 'Roles'],
            ['name' => 'eliminar_roles', 'description' => 'Eliminar roles', 'module' => 'Roles'],

            // Permisos
            ['name' => 'ver_permisos', 'description' => 'Ver lista de permisos', 'module' => 'Permisos'],
            ['name' => 'crear_permisos', 'description' => 'Crear nuevos permisos', 'module' => 'Permisos'],
            ['name' => 'editar_permisos', 'description' => 'Editar permisos existentes', 'module' => 'Permisos'],
            ['name' => 'eliminar_permisos', 'description' => 'Eliminar permisos', 'module' => 'Permisos'],

            // Docentes
            ['name' => 'ver_docentes', 'description' => 'Ver lista de docentes', 'module' => 'Docentes'],
            ['name' => 'crear_docentes', 'description' => 'Crear nuevos docentes', 'module' => 'Docentes'],
            ['name' => 'editar_docentes', 'description' => 'Editar docentes existentes', 'module' => 'Docentes'],
            ['name' => 'eliminar_docentes', 'description' => 'Eliminar docentes', 'module' => 'Docentes'],

            // Materias
            ['name' => 'ver_materias', 'description' => 'Ver lista de materias', 'module' => 'Materias'],
            ['name' => 'crear_materias', 'description' => 'Crear nuevas materias', 'module' => 'Materias'],
            ['name' => 'editar_materias', 'description' => 'Editar materias existentes', 'module' => 'Materias'],
            ['name' => 'eliminar_materias', 'description' => 'Eliminar materias', 'module' => 'Materias'],

            // Grupos
            ['name' => 'ver_grupos', 'description' => 'Ver lista de grupos', 'module' => 'Grupos'],
            ['name' => 'crear_grupos', 'description' => 'Crear nuevos grupos', 'module' => 'Grupos'],
            ['name' => 'editar_grupos', 'description' => 'Editar grupos existentes', 'module' => 'Grupos'],
            ['name' => 'eliminar_grupos', 'description' => 'Eliminar grupos', 'module' => 'Grupos'],

            // Horarios
            ['name' => 'ver_horarios', 'description' => 'Ver horarios', 'module' => 'Horarios'],
            ['name' => 'crear_horarios', 'description' => 'Crear nuevos horarios', 'module' => 'Horarios'],
            ['name' => 'editar_horarios', 'description' => 'Editar horarios existentes', 'module' => 'Horarios'],
            ['name' => 'eliminar_horarios', 'description' => 'Eliminar horarios', 'module' => 'Horarios'],

            // Asistencias
            ['name' => 'ver_asistencias', 'description' => 'Ver registros de asistencia', 'module' => 'Asistencias'],
            ['name' => 'crear_asistencias', 'description' => 'Registrar asistencias', 'module' => 'Asistencias'],
            ['name' => 'eliminar_asistencias', 'description' => 'Eliminar registros de asistencia', 'module' => 'Asistencias'],

            // Reportes
            ['name' => 'exportar_horarios', 'description' => 'Exportar horarios semanales', 'module' => 'Reportes'],
            ['name' => 'exportar_asistencias', 'description' => 'Exportar reportes de asistencia', 'module' => 'Reportes'],

            // Sistema
            ['name' => 'acceso_dashboard', 'description' => 'Acceder al dashboard', 'module' => 'Sistema'],
            ['name' => 'gestionar_perfil', 'description' => 'Editar perfil propio', 'module' => 'Sistema'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['description' => $permission['description'], 'module' => $permission['module']]
            );
        }

        $this->command->info('✅ Permisos creados exitosamente!');
    }
}

