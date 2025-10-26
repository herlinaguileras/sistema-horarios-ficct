<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\DashboardController;

// Welcome Route
Route::get('/', function () {
    return view('welcome');
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

    // Gestión Docentes (using resource is cleaner)
    Route::resource('docentes', DocenteController::class)->except(['show']); // We don't usually need a 'show' page for admin management lists

    // Gestión Materias
    Route::resource('materias', MateriaController::class)->except(['show']);

    // Gestión Aulas
    Route::resource('aulas', AulaController::class)->except(['show']);

    // Gestión Grupos (Carga Horaria)
    Route::resource('grupos', GrupoController::class)->except(['show']);

    // Gestión Horarios (Nested under Grupos)
    Route::resource('grupos.horarios', HorarioController::class)->except(['show'])->shallow();

    // Gestión Asistencias (Nested under Horarios)
    Route::resource('horarios.asistencias', AsistenciaController::class)->except(['show', 'edit', 'update'])->shallow(); // We decided against editing attendance


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

    // Route to display the QR code for a specific Horario
    Route::get('/horarios/{horario}/qr', [HorarioController::class, 'showQrCode'])
        ->middleware(['auth', 'verified'])
        ->name('horarios.qr');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
