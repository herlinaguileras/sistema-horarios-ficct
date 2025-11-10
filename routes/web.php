<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\SemestreController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

// Welcome Route - Redirect to login/dashboard
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Dashboard Routes (accessible to any logged-in user for now)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');
Route::get('/dashboard/export/horario-semanal', [DashboardController::class, 'exportHorarioSemanal'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.export.horario');
Route::get('/dashboard/export/horario-semanal-pdf', [DashboardController::class, 'exportHorarioSemanalPdf'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.export.horario.pdf');
Route::get('/dashboard/export/asistencia', [DashboardController::class, 'exportAsistencia'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.export.asistencia');
Route::get('/dashboard/export/asistencia-pdf', [DashboardController::class, 'exportAsistenciaPdf'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.export.asistencia.pdf');


// --- PROTECTED ADMIN ROUTES ---
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () { // <-- START GROUP

    // Gestión de Usuarios y Roles
    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('/users/{user}/toggle-estado', [UserController::class, 'toggleEstado'])->name('users.toggle-estado');
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::patch('/roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');

    // Gestión Docentes (using resource is cleaner)
    Route::resource('docentes', DocenteController::class)->except(['show']); // We don't usually need a 'show' page for admin management lists

    // Gestión Materias
    Route::resource('materias', MateriaController::class)->except(['show']);

    // Gestión Aulas
    Route::resource('aulas', AulaController::class)->except(['show']);

    // Gestión Grupos (Carga Horaria)
    Route::resource('grupos', GrupoController::class)->except(['show']);

    // Gestión Semestres
    Route::resource('semestres', SemestreController::class);
    Route::patch('/semestres/{semestre}/toggle-activo', [SemestreController::class, 'toggleActivo'])->name('semestres.toggle-activo');

    // Gestión Horarios (Módulo Independiente)
    Route::resource('horarios', HorarioController::class)->except(['show']);

    // Gestión de Estadísticas
    Route::get('/estadisticas', [App\Http\Controllers\EstadisticaController::class, 'index'])->name('estadisticas.index');
    Route::get('/estadisticas/{docente}', [App\Http\Controllers\EstadisticaController::class, 'show'])->name('estadisticas.show');

});

// Profile Routes (accessible to any logged-in user)
Route::middleware('auth')->group(function () {
    // Nueva ruta para que el docente marque asistencia (botón)
    Route::post('/asistencias/marcar/{horario}', [AsistenciaController::class, 'marcarAsistencia'])
        ->middleware(['auth', 'verified'])
        ->name('asistencias.marcar');

    // CORREGIDO: Cambiar GET a POST por seguridad (CSRF protection)
    Route::post('/asistencias/marcar-qr/{horario}', [AsistenciaController::class, 'marcarAsistenciaQr'])
        ->middleware(['auth', 'verified'])
        ->name('asistencias.marcar.qr');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
