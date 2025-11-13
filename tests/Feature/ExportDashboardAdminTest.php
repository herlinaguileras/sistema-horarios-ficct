<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Semestre;
use App\Models\Docente;
use App\Models\Materia;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Aula;
use App\Models\Asistencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HorarioSemanalExport;
use App\Exports\AsistenciaExport;

class ExportDashboardAdminTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $semestreActivo;
    protected $docente;
    protected $materia;
    protected $grupo;
    protected $aula;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear rol admin
        $adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrador'
        ]);

        // Crear usuario admin
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com'
        ]);
        $this->admin->roles()->attach($adminRole->id);

        // Crear semestre activo
        $this->semestreActivo = Semestre::create([
            'nombre' => '2-2025',
            'fecha_inicio' => '2025-08-01',
            'fecha_fin' => '2025-12-20',
            'estado' => 'Activo'
        ]);

        // Crear datos de prueba
        $userDocente = User::factory()->create(['name' => 'Test Docente']);
        $this->docente = Docente::create([
            'user_id' => $userDocente->id,
            'codigo_docente' => '100',
            'carnet_identidad' => '12345678'
        ]);

        $this->materia = Materia::create([
            'sigla' => 'MAT101',
            'nombre' => 'Matemáticas I',
            'nivel_semestre' => 1
        ]);

        $this->grupo = Grupo::create([
            'nombre' => 'A',
            'materia_id' => $this->materia->id,
            'docente_id' => $this->docente->id,
            'semestre_id' => $this->semestreActivo->id
        ]);

        $this->aula = Aula::create([
            'nombre' => '101',
            'capacidad' => 30,
            'piso' => 1,
            'tipo' => 'Aula'
        ]);
    }

    /** @test */
    public function test_export_horario_excel_sin_filtros()
    {
        Excel::fake();

        // Crear horarios
        Horario::create([
            'grupo_id' => $this->grupo->id,
            'aula_id' => $this->aula->id,
            'dia_semana' => 1,
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.export.horario'));

        $response->assertStatus(200);
        Excel::assertDownloaded('horario_semanal_' . $this->semestreActivo->nombre . '.xlsx');
    }

    /** @test */
    public function test_export_horario_excel_con_filtro_docente()
    {
        Excel::fake();

        // Crear otro docente
        $userDocente2 = User::factory()->create(['name' => 'Otro Docente']);
        $docente2 = Docente::create([
            'user_id' => $userDocente2->id,
            'codigo_docente' => '101',
            'carnet_identidad' => '87654321'
        ]);

        $grupo2 = Grupo::create([
            'nombre' => 'B',
            'materia_id' => $this->materia->id,
            'docente_id' => $docente2->id,
            'semestre_id' => $this->semestreActivo->id
        ]);

        // Crear horarios para ambos docentes
        Horario::create([
            'grupo_id' => $this->grupo->id,
            'aula_id' => $this->aula->id,
            'dia_semana' => 1,
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00'
        ]);

        Horario::create([
            'grupo_id' => $grupo2->id,
            'aula_id' => $this->aula->id,
            'dia_semana' => 2,
            'hora_inicio' => '10:00',
            'hora_fin' => '12:00'
        ]);

        // Exportar filtrando por primer docente
        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.export.horario', [
                'filtro_docente_id' => $this->docente->id
            ]));

        $response->assertStatus(200);
        Excel::assertDownloaded('horario_semanal_' . $this->semestreActivo->nombre . '.xlsx');
    }

    /** @test */
    public function test_export_horario_excel_con_filtro_dia()
    {
        Excel::fake();

        // Crear horarios en diferentes días
        Horario::create([
            'grupo_id' => $this->grupo->id,
            'aula_id' => $this->aula->id,
            'dia_semana' => 1, // Lunes
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00'
        ]);

        Horario::create([
            'grupo_id' => $this->grupo->id,
            'aula_id' => $this->aula->id,
            'dia_semana' => 3, // Miércoles
            'hora_inicio' => '10:00',
            'hora_fin' => '12:00'
        ]);

        // Exportar solo Lunes
        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.export.horario', [
                'filtro_dia_semana' => 1
            ]));

        $response->assertStatus(200);
        Excel::assertDownloaded('horario_semanal_' . $this->semestreActivo->nombre . '.xlsx');
    }

    /** @test */
    public function test_export_horario_pdf_sin_filtros()
    {
        // Crear horarios
        Horario::create([
            'grupo_id' => $this->grupo->id,
            'aula_id' => $this->aula->id,
            'dia_semana' => 1,
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.export.horario.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function test_export_horario_pdf_con_filtros()
    {
        // Crear horarios
        Horario::create([
            'grupo_id' => $this->grupo->id,
            'aula_id' => $this->aula->id,
            'dia_semana' => 1,
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.export.horario.pdf', [
                'filtro_docente_id' => $this->docente->id,
                'filtro_dia_semana' => 1
            ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function test_export_asistencia_excel_sin_filtros()
    {
        Excel::fake();

        // Crear horario y asistencia
        $horario = Horario::create([
            'grupo_id' => $this->grupo->id,
            'aula_id' => $this->aula->id,
            'dia_semana' => 1,
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00'
        ]);

        Asistencia::create([
            'docente_id' => $this->docente->id,
            'horario_id' => $horario->id,
            'fecha' => '2025-11-13',
            'hora_registro' => '08:05:00',
            'estado' => 'Presente',
            'metodo_registro' => 'QR'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.export.asistencia'));

        $response->assertStatus(200);
        Excel::assertDownloaded('asistencia_' . $this->semestreActivo->nombre . '.xlsx');
    }

    /** @test */
    public function test_export_asistencia_excel_con_filtro_estado()
    {
        Excel::fake();

        // Crear horario
        $horario = Horario::create([
            'grupo_id' => $this->grupo->id,
            'aula_id' => $this->aula->id,
            'dia_semana' => 1,
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00'
        ]);

        // Crear asistencias con diferentes estados
        Asistencia::create([
            'docente_id' => $this->docente->id,
            'horario_id' => $horario->id,
            'fecha' => '2025-11-13',
            'hora_registro' => '08:05:00',
            'estado' => 'Presente',
            'metodo_registro' => 'QR'
        ]);

        Asistencia::create([
            'docente_id' => $this->docente->id,
            'horario_id' => $horario->id,
            'fecha' => '2025-11-14',
            'hora_registro' => '08:05:00',
            'estado' => 'Ausente',
            'metodo_registro' => 'Manual'
        ]);

        // Exportar solo Presentes
        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.export.asistencia', [
                'filtro_asist_estado' => 'Presente'
            ]));

        $response->assertStatus(200);
        Excel::assertDownloaded('asistencia_' . $this->semestreActivo->nombre . '.xlsx');
    }

    /** @test */
    public function test_export_asistencia_excel_con_filtro_fechas()
    {
        Excel::fake();

        // Crear horario
        $horario = Horario::create([
            'grupo_id' => $this->grupo->id,
            'aula_id' => $this->aula->id,
            'dia_semana' => 1,
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00'
        ]);

        // Crear asistencias en diferentes fechas
        Asistencia::create([
            'docente_id' => $this->docente->id,
            'horario_id' => $horario->id,
            'fecha' => '2025-11-01',
            'hora_registro' => '08:05:00',
            'estado' => 'Presente',
            'metodo_registro' => 'QR'
        ]);

        Asistencia::create([
            'docente_id' => $this->docente->id,
            'horario_id' => $horario->id,
            'fecha' => '2025-11-15',
            'hora_registro' => '08:05:00',
            'estado' => 'Presente',
            'metodo_registro' => 'QR'
        ]);

        // Exportar con rango de fechas
        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.export.asistencia', [
                'filtro_asist_fecha_inicio' => '2025-11-01',
                'filtro_asist_fecha_fin' => '2025-11-10'
            ]));

        $response->assertStatus(200);
        Excel::assertDownloaded('asistencia_' . $this->semestreActivo->nombre . '.xlsx');
    }

    /** @test */
    public function test_export_asistencia_pdf_sin_filtros()
    {
        // Crear horario y asistencia
        $horario = Horario::create([
            'grupo_id' => $this->grupo->id,
            'aula_id' => $this->aula->id,
            'dia_semana' => 1,
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00'
        ]);

        Asistencia::create([
            'docente_id' => $this->docente->id,
            'horario_id' => $horario->id,
            'fecha' => '2025-11-13',
            'hora_registro' => '08:05:00',
            'estado' => 'Presente',
            'metodo_registro' => 'QR'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.export.asistencia.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function test_export_asistencia_pdf_con_filtros()
    {
        // Crear horario y asistencia
        $horario = Horario::create([
            'grupo_id' => $this->grupo->id,
            'aula_id' => $this->aula->id,
            'dia_semana' => 1,
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00'
        ]);

        Asistencia::create([
            'docente_id' => $this->docente->id,
            'horario_id' => $horario->id,
            'fecha' => '2025-11-13',
            'hora_registro' => '08:05:00',
            'estado' => 'Presente',
            'metodo_registro' => 'QR'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.export.asistencia.pdf', [
                'filtro_asist_estado' => 'Presente',
                'filtro_asist_metodo' => 'QR'
            ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function test_export_sin_semestre_activo_falla()
    {
        // Desactivar semestre
        $this->semestreActivo->update(['estado' => 'Inactivo']);

        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.export.horario'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHasErrors('export_error');
    }

    /** @test */
    public function test_export_requiere_autenticacion()
    {
        $response = $this->get(route('dashboard.export.horario'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('dashboard.export.asistencia'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_bitacora_registra_exportaciones()
    {
        Excel::fake();

        // Crear horario
        Horario::create([
            'grupo_id' => $this->grupo->id,
            'aula_id' => $this->aula->id,
            'dia_semana' => 1,
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00'
        ]);

        $this->actingAs($this->admin)
            ->get(route('dashboard.export.horario'));

        // Verificar que se registró en bitácora
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'EXPORT_horario_semanal'
        ]);
    }
}
