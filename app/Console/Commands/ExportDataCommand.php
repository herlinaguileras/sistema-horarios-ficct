<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;
use App\Models\Docente;
use App\Models\Materia;
use App\Models\Aula;
use App\Models\Semestre;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Titulo;

class ExportDataCommand extends Command
{
    protected $signature = 'db:export-data';
    protected $description = 'Export database data to seeder format';

    public function handle()
    {
        $this->info('🔍 Exportando datos de la base de datos local...');
        $this->info('');

        // Users
        $this->info('👥 USUARIOS:');
        $users = User::with('roles')->get();
        foreach ($users as $user) {
            $roles = $user->roles->pluck('name')->join(', ');
            $this->line("  - {$user->name} ({$user->email}) - Roles: [{$roles}]");
        }
        $this->info('');

        // Docentes
        $this->info('👨‍🏫 DOCENTES:');
        $docentes = Docente::with('user', 'titulos')->get();
        foreach ($docentes as $docente) {
            $titulos = $docente->titulos->pluck('nombre')->join(', ');
            $titulo = $titulos ?: $docente->titulo ?: 'Sin título';
            $this->line("  - {$docente->user->name} ({$docente->carnet_identidad}) - {$titulo}");
        }
        $this->info('');

        // Materias
        $this->info('📚 MATERIAS:');
        $materias = Materia::all();
        foreach ($materias as $materia) {
            $this->line("  - {$materia->nombre} ({$materia->sigla})");
        }
        $this->info('');

        // Aulas
        $this->info('🏛️ AULAS:');
        $aulas = Aula::all();
        foreach ($aulas as $aula) {
            $this->line("  - {$aula->nombre} - Piso {$aula->piso} - Capacidad: {$aula->capacidad}");
        }
        $this->info('');

        // Semestres
        $this->info('📅 SEMESTRES:');
        $semestres = Semestre::all();
        foreach ($semestres as $semestre) {
            $this->line("  - {$semestre->nombre} ({$semestre->estado})");
        }
        $this->info('');

        // Grupos
        $this->info('👥 GRUPOS:');
        $grupos = Grupo::with('materia', 'docente.user', 'semestre')->get();
        foreach ($grupos as $grupo) {
            $docente = $grupo->docente ? $grupo->docente->user->name : 'Sin docente';
            $this->line("  - {$grupo->materia->nombre} - Grupo {$grupo->numero_grupo} - {$docente}");
        }
        $this->info('');

        // Horarios
        $this->info('🕐 HORARIOS:');
        $horarios = Horario::with('grupo.materia', 'aula')->get();
        $dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        foreach ($horarios as $horario) {
            $dia = $dias[$horario->dia_semana] ?? 'N/A';
            $materia = $horario->grupo->materia->nombre ?? 'N/A';
            $aula = $horario->aula->nombre ?? 'N/A';
            $this->line("  - {$materia} - {$dia} {$horario->hora_inicio}-{$horario->hora_fin} - {$aula}");
        }

        $this->info('');
        $this->info('✅ Exportación completada');

        return 0;
    }
}
