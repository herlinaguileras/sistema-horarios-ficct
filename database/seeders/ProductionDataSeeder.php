<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Docente;
use App\Models\Materia;
use App\Models\Aula;
use App\Models\Semestre;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Titulo;

class ProductionDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando seed de producción...');

        // === 1. ROLES ===
        $this->command->info('📋 Creando roles...');
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrador del Sistema']
        );
        $docenteRole = Role::firstOrCreate(
            ['name' => 'docente'],
            ['description' => 'Docente de la Facultad']
        );

        // === 2. USUARIOS ADMIN ===
        $this->command->info('👤 Creando usuarios administradores...');

        // Admin principal
        $adminPrincipal = User::firstOrCreate(
            ['email' => 'herlinaguilera19@gmail.com'],
            [
                'name' => 'Herlin Aguilera',
                'password' => Hash::make('herlin123'), // Cambia esta contraseña
                'email_verified_at' => now(),
            ]
        );
        if (!$adminPrincipal->roles()->where('role_id', $adminRole->id)->exists()) {
            $adminPrincipal->roles()->attach($adminRole->id);
        }

        // Admin secundario
        $adminFicct = User::firstOrCreate(
            ['email' => 'admin@ficct.edu.bo'],
            [
                'name' => 'Administrador FICCT',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
        if (!$adminFicct->roles()->where('role_id', $adminRole->id)->exists()) {
            $adminFicct->roles()->attach($adminRole->id);
        }

        // === 3. USUARIOS DOCENTES ===
        $this->command->info('👨‍🏫 Creando usuarios docentes...');

        $docentesData = [
            [
                'user' => [
                    'name' => 'Ronald Dorado Suarez Salas',
                    'email' => 'juanperez@gmail.com',
                    'password' => Hash::make('docente123'),
                ],
                'docente' => [
                    'codigo_docente' => 'DOC001',
                    'carnet_identidad' => '13892602',
                    'telefono' => '70000001',
                    'titulo' => 'Licenciado en Ciencias Jurídicas',
                    'facultad' => 'FICCT',
                    'estado' => 'Activo',
                ],
                'titulos' => ['Licenciado en Ciencias Jurídicas, Sociales y Relaciones Internacionales']
            ],
            [
                'user' => [
                    'name' => 'Armando Paco',
                    'email' => 'pacoweb@gmail.com',
                    'password' => Hash::make('docente123'),
                ],
                'docente' => [
                    'codigo_docente' => 'DOC002',
                    'carnet_identidad' => '13892603',
                    'telefono' => '70000002',
                    'titulo' => 'PDH en Ingeniería de Datos',
                    'facultad' => 'FICCT',
                    'estado' => 'Activo',
                ],
                'titulos' => ['PDH en Ingeniería de Datos']
            ],
            [
                'user' => [
                    'name' => 'Yulisa Dorado Suarez',
                    'email' => 'yulidorado@gmail.com',
                    'password' => Hash::make('docente123'),
                ],
                'docente' => [
                    'codigo_docente' => 'DOC003',
                    'carnet_identidad' => '13892604',
                    'telefono' => '70000003',
                    'titulo' => 'Licenciado en Ciencias Jurídicas',
                    'facultad' => 'FICCT',
                    'estado' => 'Activo',
                ],
                'titulos' => ['Licenciado en Ciencias Jurídicas, Sociales y Relaciones Internacionales']
            ],
            [
                'user' => [
                    'name' => 'Rodrigo Perez',
                    'email' => 'rodrigo@gmail.com',
                    'password' => Hash::make('docente123'),
                ],
                'docente' => [
                    'codigo_docente' => 'DOC004',
                    'carnet_identidad' => '13892605',
                    'telefono' => '70000004',
                    'titulo' => 'Ingeniero de Sistemas',
                    'facultad' => 'FICCT',
                    'estado' => 'Activo',
                ],
                'titulos' => []
            ],
            [
                'user' => [
                    'name' => 'Jose Melgar',
                    'email' => 'jose@gmail.com',
                    'password' => Hash::make('docente123'),
                ],
                'docente' => [
                    'codigo_docente' => 'DOC005',
                    'carnet_identidad' => '13892606',
                    'telefono' => '70000005',
                    'titulo' => 'PDH en Ingeniería de Datos',
                    'facultad' => 'FICCT',
                    'estado' => 'Activo',
                ],
                'titulos' => ['PDH en Ingeniería de Datos']
            ],
        ];

        foreach ($docentesData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['user']['email']],
                array_merge($data['user'], ['email_verified_at' => now()])
            );

            if (!$user->roles()->where('role_id', $docenteRole->id)->exists()) {
                $user->roles()->attach($docenteRole->id);
            }

            $docente = Docente::firstOrCreate(
                ['user_id' => $user->id],
                $data['docente']
            );

            // Crear títulos
            foreach ($data['titulos'] as $tituloNombre) {
                Titulo::firstOrCreate(
                    ['docente_id' => $docente->id, 'nombre' => $tituloNombre],
                    [
                        'institucion' => 'Universidad',
                        'anio_obtencion' => 2020,
                    ]
                );
            }
        }

        // === 4. SEMESTRE ===
        $this->command->info('📅 Creando semestre...');
        $semestre = Semestre::firstOrCreate(
            ['nombre' => 'Gestion 2-2025'],
            [
                'fecha_inicio' => '2025-10-18',
                'fecha_fin' => '2025-12-18',
                'estado' => 'Activa',
            ]
        );

        // === 5. AULAS ===
        $this->command->info('🏛️ Creando aulas...');
        $aulas = [
            ['nombre' => '236-31', 'piso' => 1, 'capacidad' => 30, 'tipo' => 'Aula Común'],
            ['nombre' => '236-41', 'piso' => 4, 'capacidad' => 45, 'tipo' => 'Laboratorio'],
            ['nombre' => '236-15', 'piso' => 1, 'capacidad' => 30, 'tipo' => 'Aula Común'],
            ['nombre' => '236-44', 'piso' => 4, 'capacidad' => 40, 'tipo' => 'Laboratorio'],
            ['nombre' => '236-35', 'piso' => 3, 'capacidad' => 40, 'tipo' => 'Aula Común'],
        ];

        $aulasCreadas = [];
        foreach ($aulas as $aulaData) {
            $aula = Aula::firstOrCreate(
                ['nombre' => $aulaData['nombre']],
                $aulaData
            );
            $aulasCreadas[$aulaData['nombre']] = $aula;
        }

        // === 6. MATERIAS ===
        $this->command->info('📚 Creando materias...');
        $materias = [
            ['nombre' => 'Base de Datos II', 'sigla' => 'INF322', 'nivel_semestre' => 1, 'carrera' => 'Sistemas'],
            ['nombre' => 'Introducción a la Informática', 'sigla' => 'INF110', 'nivel_semestre' => 1, 'carrera' => 'Informatica'],
            ['nombre' => 'Ciencias Jurídicas de la Computación', 'sigla' => 'LEG401', 'nivel_semestre' => 4, 'carrera' => 'Sistemas'],
        ];

        $materiasCreadas = [];
        foreach ($materias as $materiaData) {
            $materia = Materia::firstOrCreate(
                ['sigla' => $materiaData['sigla']],
                $materiaData
            );
            $materiasCreadas[$materiaData['sigla']] = $materia;
        }

        // === 7. GRUPOS ===
        $this->command->info('👥 Creando grupos...');
        $gruposData = [
            ['materia_sigla' => 'INF322', 'docente_email' => 'juanperez@gmail.com', 'numero_grupo' => 'A'],
            ['materia_sigla' => 'INF110', 'docente_email' => 'juanperez@gmail.com', 'numero_grupo' => 'A'],
            ['materia_sigla' => 'INF110', 'docente_email' => 'pacoweb@gmail.com', 'numero_grupo' => 'B'],
            ['materia_sigla' => 'INF322', 'docente_email' => 'jose@gmail.com', 'numero_grupo' => 'B'],
            ['materia_sigla' => 'INF110', 'docente_email' => 'jose@gmail.com', 'numero_grupo' => 'C'],
            ['materia_sigla' => 'INF322', 'docente_email' => 'jose@gmail.com', 'numero_grupo' => 'C'],
        ];

        $gruposCreados = [];
        foreach ($gruposData as $grupoData) {
            $materia = $materiasCreadas[$grupoData['materia_sigla']];
            $docente = User::where('email', $grupoData['docente_email'])->first()->docente;

            $grupo = Grupo::firstOrCreate(
                [
                    'semestre_id' => $semestre->id,
                    'materia_id' => $materia->id,
                    'numero_grupo' => $grupoData['numero_grupo']
                ],
                [
                    'docente_id' => $docente->id,
                    'cupo_maximo' => 40,
                ]
            );
            $gruposCreados[] = $grupo;
        }

        // === 8. HORARIOS ===
        $this->command->info('🕐 Creando horarios...');
        $horariosData = [
            ['grupo_index' => 0, 'dia' => 1, 'inicio' => '08:30:00', 'fin' => '10:00:00', 'aula' => '236-31'],
            ['grupo_index' => 1, 'dia' => 3, 'inicio' => '07:00:00', 'fin' => '08:30:00', 'aula' => '236-41'],
            ['grupo_index' => 2, 'dia' => 1, 'inicio' => '07:00:00', 'fin' => '08:30:00', 'aula' => '236-44'],
            ['grupo_index' => 3, 'dia' => 2, 'inicio' => '08:15:00', 'fin' => '10:30:00', 'aula' => '236-35'],
            ['grupo_index' => 4, 'dia' => 5, 'inicio' => '13:25:00', 'fin' => '14:05:00', 'aula' => '236-31'],
            ['grupo_index' => 5, 'dia' => 5, 'inicio' => '15:00:00', 'fin' => '15:30:00', 'aula' => '236-41'],
            ['grupo_index' => 5, 'dia' => 6, 'inicio' => '10:45:00', 'fin' => '13:45:00', 'aula' => '236-41'],
        ];

        foreach ($horariosData as $horarioData) {
            if (isset($gruposCreados[$horarioData['grupo_index']])) {
                $grupo = $gruposCreados[$horarioData['grupo_index']];
                $aula = $aulasCreadas[$horarioData['aula']];

                Horario::firstOrCreate(
                    [
                        'grupo_id' => $grupo->id,
                        'dia_semana' => $horarioData['dia'],
                        'hora_inicio' => $horarioData['inicio'],
                    ],
                    [
                        'hora_fin' => $horarioData['fin'],
                        'aula_id' => $aula->id,
                    ]
                );
            }
        }

        $this->command->info('');
        $this->command->info('✅ Datos de producción creados exitosamente!');
        $this->command->info('');
        $this->command->info('👤 ADMINISTRADORES:');
        $this->command->info('  📧 Email: herlinaguilera19@gmail.com');
        $this->command->info('  🔑 Password: herlin123');
        $this->command->info('  📧 Email: admin@ficct.edu.bo');
        $this->command->info('  🔑 Password: admin123');
        $this->command->info('');
        $this->command->info('👨‍🏫 DOCENTES:');
        $this->command->info('  📧 Todos usan password: docente123');
        $this->command->info('  - Ronald Dorado: juanperez@gmail.com');
        $this->command->info('  - Armando Paco: pacoweb@gmail.com');
        $this->command->info('  - Yulisa Dorado: yulidorado@gmail.com');
        $this->command->info('  - Rodrigo Perez: rodrigo@gmail.com');
        $this->command->info('  - Jose Melgar: jose@gmail.com');
    }
}
