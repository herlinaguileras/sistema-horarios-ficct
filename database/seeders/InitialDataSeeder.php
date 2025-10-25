<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear Roles
        $roles = [
            ['name' => 'admin', 'description' => 'Administrador del Sistema'],
            ['name' => 'docente', 'description' => 'Docente de la Facultad'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                ['description' => $roleData['description']]
            );
        }

        // Crear Usuario Administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@ficct.edu.bo'],
            [
                'name' => 'Administrador FICCT',
                'password' => Hash::make('admin123'),
            ]
        );

        // Asignar rol de admin
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole && !$admin->roles->contains($adminRole->id)) {
            $admin->roles()->attach($adminRole->id);
        }

        // Crear Semestre Activo
        DB::table('semestres')->updateOrInsert(
            ['nombre' => 'Gestion 2-2025'],
            [
                'fecha_inicio' => '2025-10-18',
                'fecha_fin' => '2025-12-18',
                'estado' => 'Activa',
            ]
        );

        // Crear Aulas
        $aulas = [
            ['nombre' => '236-31', 'piso' => 1, 'capacidad' => 30, 'tipo' => 'Aula Común'],
            ['nombre' => '236-41', 'piso' => 4, 'capacidad' => 45, 'tipo' => 'Laboratorio'],
            ['nombre' => '236-15', 'piso' => 1, 'capacidad' => 30, 'tipo' => 'Aula Común'],
            ['nombre' => '236-44', 'piso' => 4, 'capacidad' => 40, 'tipo' => 'Laboratorio'],
            ['nombre' => '236-35', 'piso' => 3, 'capacidad' => 40, 'tipo' => 'Aula Común'],
        ];

        foreach ($aulas as $aula) {
            DB::table('aulas')->updateOrInsert(
                ['nombre' => $aula['nombre']],
                $aula
            );
        }

        // Crear Materias
        $materias = [
            ['nombre' => 'Base de Datos II', 'sigla' => 'INF322', 'nivel_semestre' => 1, 'carrera' => 'Sistemas'],
            ['nombre' => 'Introducción a la Informática', 'sigla' => 'INF110', 'nivel_semestre' => 1, 'carrera' => 'Informatica'],
            ['nombre' => 'Ciencias Jurídicas de la Computación', 'sigla' => 'LEG401', 'nivel_semestre' => 4, 'carrera' => 'Sistemas'],
        ];

        foreach ($materias as $materia) {
            DB::table('materias')->updateOrInsert(
                ['sigla' => $materia['sigla']],
                $materia
            );
        }

        $this->command->info('✅ Datos iniciales creados exitosamente!');
        $this->command->info('📧 Email: admin@ficct.edu.bo');
        $this->command->info('🔑 Password: admin123');
    }
}
