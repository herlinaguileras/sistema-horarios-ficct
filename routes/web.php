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
use App\Http\Controllers\DocenteDashboardController;

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
// Estas rutas están protegidas para admin, pero también permitirán acceso basado en permisos
Route::middleware(['auth', 'verified'])->group(function () { // <-- START GROUP

    // Gestión de Usuarios (requiere módulo 'usuarios')
    Route::middleware(['module:usuarios'])->group(function() {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/toggle-estado', [UserController::class, 'toggleEstado'])->name('users.toggle-estado');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Gestión de Roles (requiere módulo 'roles')
    Route::middleware(['module:roles'])->group(function() {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::patch('/roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    // Gestión Docentes (requiere módulo 'docentes')
    Route::middleware(['module:docentes'])->group(function() {
        Route::resource('docentes', DocenteController::class);
    });

    // Gestión Materias (requiere módulo 'materias')
    Route::middleware(['module:materias'])->group(function() {
        Route::resource('materias', MateriaController::class);
    });

    // Gestión Aulas (requiere módulo 'aulas')
    Route::middleware(['module:aulas'])->group(function() {
        Route::resource('aulas', AulaController::class);
    });

    // Gestión Grupos (requiere módulo 'grupos')
    Route::middleware(['module:grupos'])->group(function() {
        Route::resource('grupos', GrupoController::class);
    });

    // Gestión Semestres (requiere módulo 'semestres')
    Route::middleware(['module:semestres'])->group(function() {
        Route::resource('semestres', SemestreController::class);
        Route::patch('/semestres/{semestre}/toggle-activo', [SemestreController::class, 'toggleActivo'])->name('semestres.toggle-activo');
    });

    // Gestión Horarios (requiere módulo 'horarios')
    Route::middleware(['module:horarios'])->group(function() {
        // Importación de horarios (ANTES del resource para evitar conflictos)
        Route::get('horarios/importar', [App\Http\Controllers\HorarioImportController::class, 'index'])->name('horarios.import');
        Route::post('horarios/importar/procesar', [App\Http\Controllers\HorarioImportController::class, 'import'])->name('horarios.import.process');
        Route::get('horarios/importar/plantilla', [App\Http\Controllers\HorarioImportController::class, 'descargarPlantilla'])->name('horarios.plantilla');

        // Resource de horarios (después de las rutas específicas)
        Route::resource('horarios', HorarioController::class)->except(['show']);
    });

});

// Gestión de Estadísticas (requiere módulo 'estadisticas')
Route::middleware(['auth', 'verified', 'module:estadisticas'])->group(function () {
    Route::get('/estadisticas', [App\Http\Controllers\EstadisticaController::class, 'index'])->name('estadisticas.index');
    Route::get('/estadisticas/{docente}', [App\Http\Controllers\EstadisticaController::class, 'show'])->name('estadisticas.show');
});

// Rutas específicas para Docentes
Route::middleware(['auth', 'verified', 'role:docente'])->group(function () {
    Route::get('/docente/marcar-asistencia', [DocenteDashboardController::class, 'marcarAsistencia'])->name('docente.asistencia');
    Route::get('/docente/mis-estadisticas', [DocenteDashboardController::class, 'misEstadisticas'])->name('docente.estadisticas');
});

// Ruta pública para escaneo de QR (sin auth)
Route::get('/asistencias/qr-scan/{horario}/{token}', [AsistenciaController::class, 'escanearQR'])->name('asistencias.qr.scan');

// Profile Routes (accessible to any logged-in user)
Route::middleware('auth')->group(function () {
    // Nueva ruta para que el docente marque asistencia (botón)
    Route::post('/asistencias/marcar/{horario}', [AsistenciaController::class, 'marcarAsistencia'])
        ->middleware(['auth', 'verified'])
        ->name('asistencias.marcar');

    // Ruta para generar el QR
    Route::get('/asistencias/generar-qr/{horario}', [AsistenciaController::class, 'generarQR'])
        ->middleware(['auth', 'verified'])
        ->name('asistencias.generar.qr');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
