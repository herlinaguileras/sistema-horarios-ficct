<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Semestre;
use App\Models\Horario;
use App\Models\Asistencia;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Aula;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HorarioSemanalExport;
use App\Exports\AsistenciaExport;

class ExportacionDashboardTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $semestreActivo;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear rol admin
        $adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrador del sistema',
            'level' => 100,
            'status' => 'Activo'
        ]);

        // Crear usuario admin
        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'is_active' => true
        ]);

        $this->admin->roles()->attach($adminRole->id);

        // Crear semestre activo
        $this->semestreActivo = Semestre::create([
            'nombre' => '2-2025',
            'fecha_inicio' => now()->subMonth(),
            'fecha_fin' => now()->addMonth(),
            'estado' => 'Activo'
        ]);
    }

    /** @test */
    public function test_export_horario_excel_requiere_autenticacion()
    {
        $response = $this->get(route('dashboard.export.horario'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_export_horario_pdf_requiere_autenticacion()
    {
        $response = $this->get(route('dashboard.export.horario.pdf'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_export_asistencia_excel_requiere_autenticacion()
    {
        $response = $this->get(route('dashboard.export.asistencia'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_export_asistencia_pdf_requiere_autenticacion()
    {
        $response = $this->get(route('dashboard.export.asistencia.pdf'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_export_horario_excel_funciona_con_semestre_activo()
    {
        Excel::fake();

        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.export.horario'));

        Excel::assertDownloaded('horario_semanal_2-2025.xlsx', function(HorarioSemanalExport $export) {
            return true;
        });
    }

    /** @test */
    public function test_export_horario_pdf_funciona_con_semestre_activo()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.export.horario.pdf'));

        // Verifica que retorna una respuesta de descarga
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function test_export_asistencia_excel_funciona_con_semestre_activo()
    {
        Excel::fake();

        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.export.asistencia'));

        Excel::assertDownloaded('asistencia_2-2025.xlsx', function(AsistenciaExport $export) {
            return true;
        });
    }

    /** @test */
    public function test_export_asistencia_pdf_funciona_con_semestre_activo()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.export.asistencia.pdf'));

        // Verifica que retorna una respuesta de descarga
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function test_export_horario_excel_falla_sin_semestre_activo()
    {
        // Desactivar el semestre
        $this->semestreActivo->update(['estado' => 'Inactivo']);

        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.export.horario'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHasErrors('export_error');
    }

    /** @test */
    public function test_export_asistencia_excel_falla_sin_semestre_activo()
    {
        // Desactivar el semestre
        $this->semestreActivo->update(['estado' => 'Inactivo']);

        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.export.asistencia'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHasErrors('export_error');
    }

    /** @test */
    public function test_export_horario_excel_con_filtros()
    {
        Excel::fake();

        // Crear datos de prueba
        $materia = Materia::create([
            'codigo' => 'MAT101',
            'sigla' => 'MAT',
            'nombre' => 'Matemáticas',
            'descripcion' => 'Test'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.export.horario', [
                'filtro_materia_id' => $materia->id
            ]));

        Excel::assertDownloaded('horario_semanal_2-2025.xlsx');
    }

    /** @test */
    public function test_export_asistencia_excel_con_filtros()
    {
        Excel::fake();

        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.export.asistencia', [
                'filtro_asist_estado' => 'Presente'
            ]));

        Excel::assertDownloaded('asistencia_2-2025.xlsx');
    }

    /** @test */
    public function test_exportacion_registra_en_bitacora()
    {
        Excel::fake();

        // Realizar exportación
        $this->actingAs($this->admin)
            ->get(route('dashboard.export.horario'));

        // Verificar que se registró en bitácora
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'EXPORT_horario_semanal'
        ]);
    }

    /** @test */
    public function test_log_export_recibe_parametros_correctos()
    {
        Excel::fake();

        // Realizar exportación Excel
        $this->actingAs($this->admin)
            ->get(route('dashboard.export.horario'));

        // Verificar log con estructura correcta
        $log = \App\Models\AuditLog::where('user_id', $this->admin->id)
            ->where('action', 'EXPORT_horario_semanal')
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('export', $log->details['action_type']);
        $this->assertEquals('horario_semanal', $log->details['export_type']);
        $this->assertArrayHasKey('records_exported', $log->details);
        $this->assertEquals('xlsx', $log->details['format']);
    }

    /** @test */
    public function test_export_pdf_registra_cantidad_correcta()
    {
        // Crear algunos horarios de prueba
        $docente = Docente::create([
            'user_id' => $this->admin->id,
            'codigo' => 'DOC001',
            'telefono' => '12345678',
            'titulo' => 'Ing.'
        ]);

        $materia = Materia::create([
            'codigo' => 'MAT101',
            'sigla' => 'MAT',
            'nombre' => 'Matemáticas',
            'descripcion' => 'Test'
        ]);

        $grupo = Grupo::create([
            'nombre' => 'A',
            'semestre_id' => $this->semestreActivo->id,
            'docente_id' => $docente->id,
            'materia_id' => $materia->id
        ]);

        $aula = Aula::create([
            'nombre' => 'Aula 1',
            'capacidad' => 30,
            'piso' => 1,
            'tipo' => 'Aula'
        ]);

        Horario::create([
            'grupo_id' => $grupo->id,
            'aula_id' => $aula->id,
            'dia_semana' => 1,
            'hora_inicio' => '08:00:00',
            'hora_fin' => '10:00:00'
        ]);

        Horario::create([
            'grupo_id' => $grupo->id,
            'aula_id' => $aula->id,
            'dia_semana' => 2,
            'hora_inicio' => '08:00:00',
            'hora_fin' => '10:00:00'
        ]);

        // Exportar PDF
        $this->actingAs($this->admin)
            ->get(route('dashboard.export.horario.pdf'));

        // Verificar log
        $log = \App\Models\AuditLog::where('user_id', $this->admin->id)
            ->where('action', 'EXPORT_horario_semanal')
            ->latest()
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals(2, $log->details['records_exported']);
    }
}
