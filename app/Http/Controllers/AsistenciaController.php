<?php

namespace App\Http\Controllers;

use App\Models\Asistencia; // For the model binding
use App\Models\Horario;    // For getting the Horario before deleting
use Illuminate\Http\Request; // Likely already there
use Carbon\Carbon;
use App\Models\AuditLog; // <-- NUEVO
use Illuminate\Support\Facades\Auth; // <-- NUEVO

class AsistenciaController extends Controller
{
/**
 * Muestra la lista de asistencias para un horario específico.
 */
public function index(Horario $horario)
{
    // 1. Laravel nos da el $horario gracias a la ruta anidada.

    // 2. Cargamos las asistencias de ESE horario, ordenadas por fecha y hora.
    //    También cargamos la relación 'docente.user' para mostrar el nombre.
    $asistencias = $horario->asistencias()
                          ->with('docente.user') // Carga eficiente
                          ->orderBy('fecha', 'desc')
                          ->orderBy('hora_registro', 'desc')
                          ->get();

    // 3. Devolvemos la vista y le pasamos el horario y sus asistencias
    return view('asistencias.index', [
        'horario' => $horario,
        'asistencias' => $asistencias,
    ]);
}
    /**
 * Muestra el formulario para registrar una nueva asistencia manualmente.
 */
public function create(Horario $horario)
{
    // Laravel nos da el $horario para el cual registraremos asistencia.

    // Devolvemos la vista del formulario y le pasamos el horario.
    return view('asistencias.create', ['horario' => $horario]);
}
 /**
     * Almacena un nuevo registro de asistencia en la base de datos, validando coherencia.
     */
    public function store(Request $request, Horario $horario)
    {
        // 1. VALIDAMOS LOS DATOS BÁSICOS DEL FORMULARIO
        $validatedData = $request->validate([ // <--- THIS BLOCK WAS MISSING
            'fecha' => ['required', 'date'],
            'hora_registro' => ['required', 'date_format:H:i'],
            'estado' => ['required', 'string'],
            'metodo_registro' => ['required', 'string'],
            'justificacion' => ['required', 'string', 'min:10'],
        ]);

        // --- INICIO DE LA NUEVA VALIDACIÓN DE COHERENCIA ---

        // Convertimos la fecha ingresada a un objeto Carbon
        $fechaIngresada = Carbon::parse($validatedData['fecha']);
        $horaIngresada = Carbon::parse($validatedData['hora_registro'])->format('H:i:s');
        $horaInicioClase = Carbon::parse($horario->hora_inicio)->format('H:i:s');
        $horaFinClase = Carbon::parse($horario->hora_fin)->format('H:i:s');

        // 2a. VALIDAMOS EL DÍA DE LA SEMANA (NUMÉRICO)
        $numeroDiaIngresado = $fechaIngresada->dayOfWeekIso; // 1=Lunes, 7=Domingo

        if ($numeroDiaIngresado != $horario->dia_semana) {
            $nombresDias = [ 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
            return back()->withErrors([
                'fecha' => 'La fecha ingresada ('.$nombresDias[$numeroDiaIngresado].') no coincide con el día programado para esta clase ('.$nombresDias[$horario->dia_semana].').'
            ])->withInput();
        }

    // 2b. VALIDAMOS LA HORA (Dentro de la ventana permitida: -15min / +15min desde el inicio)
    $margenMinutos = 15;
    // Calcula el inicio restando 15 min
    $inicioVentana = Carbon::parse($horario->hora_inicio)->subMinutes($margenMinutos)->format('H:i:s');
    // Calcula el fin sumando 15 min
    $finVentana    = Carbon::parse($horario->hora_inicio)->addMinutes($margenMinutos)->format('H:i:s');

    // Compara si la hora ingresada está FUERA de la ventana
    if ($horaIngresada < $inicioVentana || $horaIngresada > $finVentana) {
         // Si está fuera, devuelve el error con el rango calculado
         return back()->withErrors([
            'hora_registro' => 'La hora de registro ('.$horaIngresada.') está fuera de la ventana permitida para marcar asistencia ('.$inicioVentana.' - '.$finVentana.').'
        ])->withInput();
    }
        // --- FIN DE LA NUEVA VALIDACIÓN DE COHERENCIA ---


        // 3. AÑADIMOS LOS DATOS FALTANTES (IDs)
        $validatedData['horario_id'] = $horario->id;
        $validatedData['docente_id'] = $horario->grupo->docente_id;

     // 4. CREAMOS EL REGISTRO DE ASISTENCIA
$asistencia = Asistencia::create($validatedData);

$asistencia->refresh(); // <-- AÑADE ESTA LÍNEA PARA RECARGAR DESDE LA BD

// --- INICIO: REGISTRO EN BITÁCORA DE AUDITORÍA ---
AuditLog::create([
    'user_id' => Auth::id(),
    'action' => 'manual_attendance_create',
    'model_type' => Asistencia::class,
    'model_id' => $asistencia->id, // Ahora debería funcionar
    'details' => 'Justificación: ' . $validatedData['justificacion'],
    'ip_address' => $request->ip(),
    'user_agent' => $request->userAgent(),
]);
    // --- FIN: REGISTRO EN BITÁCORA DE AUDITORÍA ---
        // 5. REDIRIGIMOS A LA LISTA
        return redirect()
            ->route('horarios.asistencias.index', $horario)
            ->with('status', '¡Asistencia registrada exitosamente!');


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

   /**
 * Elimina el registro de asistencia especificado.
 */
public function destroy(Asistencia $asistencia) // Use Route Model Binding
{
    // Guardamos el horario al que pertenecía para la redirección
    $horario = $asistencia->horario;

    // 1. ELIMINAMOS EL REGISTRO DE ASISTENCIA
    $asistencia->delete();

    // 2. REDIRIGIMOS A LA LISTA DE ASISTENCIAS DE ESE HORARIO
    return redirect()
        ->route('horarios.asistencias.index', $horario) // Redirect back to THAT horario's list
        ->with('status', '¡Registro de asistencia eliminado exitosamente!');
}


/**
 * Registra la asistencia del docente para un horario específico (marcado por botón).
 */
/**
     * Registra la asistencia del docente para un horario específico (marcado por botón).
     */
    public function marcarAsistencia(Request $request, Horario $horario)
    {
        // 1. Obtener datos actuales
        $now = Carbon::now();
        $todayDate = $now->toDateString(); // Fecha actual 'YYYY-MM-DD'
        $currentTime = $now->format('H:i:s'); // Hora actual 'HH:MM:SS'
        $docente = Auth::user()->docente; // Obtener el perfil docente del usuario logueado

        // Verificación básica: ¿Es el docente correcto?
        if (!$docente || $horario->grupo->docente_id !== $docente->id) {
             abort(403, 'No autorizado para marcar esta asistencia.');
        }

        // 2. Validar ventana de tiempo (-15/+15 min desde inicio)
        $margenMinutos = 15;
        $inicioVentana = Carbon::parse($horario->hora_inicio)->subMinutes($margenMinutos)->format('H:i:s');
        $finVentana    = Carbon::parse($horario->hora_inicio)->addMinutes($margenMinutos)->format('H:i:s');

        if ($currentTime < $inicioVentana || $currentTime > $finVentana) {
            // Si está fuera de la ventana, redirige con error
            return redirect()->route('dashboard') // Redirige al dashboard del docente
                   // Use a specific error key related to the horario ID
                   ->withErrors(['asistencia_error_'.$horario->id => 'No puede marcar asistencia fuera de la ventana permitida ('.$inicioVentana.' - '.$finVentana.').']);
        }

        // 3. Validar si ya marcó asistencia HOY para ESTA clase
        $alreadyMarked = Asistencia::where('horario_id', $horario->id)
                                  ->where('docente_id', $docente->id)
                                  ->where('fecha', $todayDate)
                                  ->exists();

        if ($alreadyMarked) {
            // Si ya marcó, redirige con advertencia
             return redirect()->route('dashboard')
                   // Use a specific error key related to the horario ID
                   ->withErrors(['asistencia_error_'.$horario->id => 'Ya ha marcado asistencia para esta clase hoy.']);
        }

        // 4. Si pasa todas las validaciones, CREAR el registro
        Asistencia::create([
            'horario_id' => $horario->id,
            'docente_id' => $docente->id,
            'fecha' => $todayDate,
            'hora_registro' => $currentTime,
            'estado' => 'Presente', // Por defecto al marcar
            'metodo_registro' => 'Boton', // Indicar que fue por botón
            // 'justificacion' queda null
        ]);

        // 5. Redirigir de vuelta al dashboard con mensaje de éxito
        return redirect()->route('dashboard')
               ->with('status', '¡Asistencia para '.$horario->grupo->materia->sigla.' marcada exitosamente!');
    }


/**
 * Registra la asistencia del docente via QR scan (GET request).
 */
public function marcarAsistenciaQr(Request $request, Horario $horario) // Still use Request if needed later
{
    // 1. Obtener datos actuales
    $now = Carbon::now();
    $todayDate = $now->toDateString();
    $currentTime = $now->format('H:i:s');
    $docente = Auth::user()->docente;

    // Verificación básica: ¿Es el docente correcto?
    if (!$docente || $horario->grupo->docente_id !== $docente->id) {
         abort(403, 'No autorizado para marcar esta asistencia.');
    }

    // 2. Validar ventana de tiempo (-15/+15 min desde inicio)
    $margenMinutos = 15;
    $inicioVentana = Carbon::parse($horario->hora_inicio)->subMinutes($margenMinutos)->format('H:i:s');
    $finVentana    = Carbon::parse($horario->hora_inicio)->addMinutes($margenMinutos)->format('H:i:s');

    if ($currentTime < $inicioVentana || $currentTime > $finVentana) {
        return redirect()->route('dashboard')
               ->withErrors(['asistencia_error_'.$horario->id => 'QR: No puede marcar asistencia fuera de la ventana permitida ('.$inicioVentana.' - '.$finVentana.').']);
    }

    // 3. Validar si ya marcó asistencia HOY para ESTA clase
    $alreadyMarked = Asistencia::where('horario_id', $horario->id)
                              ->where('docente_id', $docente->id)
                              ->where('fecha', $todayDate)
                              ->exists();

    if ($alreadyMarked) {
         return redirect()->route('dashboard')
               ->withErrors(['asistencia_error_'.$horario->id => 'QR: Ya ha marcado asistencia para esta clase hoy.']);
    }

    // 4. CREAR el registro (Marcar como 'QR')
    Asistencia::create([
        'horario_id' => $horario->id,
        'docente_id' => $docente->id,
        'fecha' => $todayDate,
        'hora_registro' => $currentTime,
        'estado' => 'Presente',
        'metodo_registro' => 'QR', // <-- Indicate QR method
        // 'justificacion' queda null
    ]);

    // 5. Redirigir de vuelta al dashboard con mensaje de éxito
    return redirect()->route('dashboard')
           ->with('status', '¡Asistencia QR para '.$horario->grupo->materia->sigla.' marcada exitosamente!');
}

}
