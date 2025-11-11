<?php

namespace App\Http\Controllers;

use App\Models\Asistencia; // For the model binding
use App\Models\Horario;    // For getting the Horario before deleting
use Illuminate\Http\Request; // Likely already there
use Carbon\Carbon;
use App\Models\AuditLog; // <-- NUEVO
use Illuminate\Support\Facades\Auth; // <-- NUEVO
use Illuminate\Support\Facades\Hash; // Para verificación de contraseña
use SimpleSoftwareIO\QrCode\Facades\QrCode; // Para generar códigos QR
use Illuminate\Support\Facades\URL; // Para URLs firmadas

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
     * Registra la asistencia del docente manualmente con verificación de contraseña.
     */
    public function marcarAsistencia(Request $request, Horario $horario)
    {
        // 1. Validar la contraseña ingresada
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        // Verificar que la contraseña sea correcta
        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors([
                'password_error_'.$horario->id => 'La contraseña ingresada es incorrecta.'
            ])->withInput();
        }

        // 2. Obtener datos actuales
        $now = Carbon::now();
        $todayDate = $now->toDateString();
        $currentTime = $now->format('H:i:s');
        $docente = Auth::user()->docente;

        // Verificación básica: ¿Es el docente correcto?
        if (!$docente || $horario->grupo->docente_id !== $docente->id) {
             abort(403, 'No autorizado para marcar esta asistencia.');
        }

        // 3. Validar ventana de tiempo (-15/+15 min desde inicio)
        $margenMinutos = 15;
        $inicioVentana = Carbon::parse($horario->hora_inicio)->subMinutes($margenMinutos)->format('H:i:s');
        $finVentana    = Carbon::parse($horario->hora_inicio)->addMinutes($margenMinutos)->format('H:i:s');

        if ($currentTime < $inicioVentana || $currentTime > $finVentana) {
            return back()->withErrors([
                'asistencia_error_'.$horario->id => 'No puede marcar asistencia fuera de la ventana permitida ('.$inicioVentana.' - '.$finVentana.').'
            ])->withInput();
        }

        // 4. Validar si ya marcó asistencia HOY para ESTA clase
        $alreadyMarked = Asistencia::where('horario_id', $horario->id)
                                  ->where('docente_id', $docente->id)
                                  ->where('fecha', $todayDate)
                                  ->exists();

        if ($alreadyMarked) {
             return back()->withErrors([
                'asistencia_error_'.$horario->id => 'Ya ha marcado asistencia para esta clase hoy.'
            ])->withInput();
        }

        // 5. Si pasa todas las validaciones, CREAR el registro
        Asistencia::create([
            'horario_id' => $horario->id,
            'docente_id' => $docente->id,
            'fecha' => $todayDate,
            'hora_registro' => $currentTime,
            'estado' => 'Presente',
            'metodo_registro' => 'Manual',
            // 'justificacion' queda null
        ]);

        // 6. Redirigir de vuelta al dashboard con mensaje de éxito
        return back()->with('status', '¡Asistencia para '.$horario->grupo->materia->sigla.' marcada exitosamente!');
    }


/**
 * Registra la asistencia del docente via QR (sin contraseña).
 */
public function marcarAsistenciaQr(Request $request, Horario $horario)
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
        return back()->withErrors([
            'asistencia_error_'.$horario->id => 'No puede marcar asistencia fuera de la ventana permitida ('.$inicioVentana.' - '.$finVentana.').'
        ]);
    }

    // 3. Validar si ya marcó asistencia HOY para ESTA clase
    $alreadyMarked = Asistencia::where('horario_id', $horario->id)
                              ->where('docente_id', $docente->id)
                              ->where('fecha', $todayDate)
                              ->exists();

    if ($alreadyMarked) {
         return back()->withErrors([
            'asistencia_error_'.$horario->id => 'Ya ha marcado asistencia para esta clase hoy.'
        ]);
    }

    // 4. CREAR el registro (Marcar como 'QR')
    Asistencia::create([
        'horario_id' => $horario->id,
        'docente_id' => $docente->id,
        'fecha' => $todayDate,
        'hora_registro' => $currentTime,
        'estado' => 'Presente',
        'metodo_registro' => 'QR',
        // 'justificacion' queda null
    ]);

    // 5. Redirigir de vuelta con mensaje de éxito
    return back()->with('status', '¡Asistencia para '.$horario->grupo->materia->sigla.' marcada exitosamente con QR!');
}

/**
 * Genera un código QR para marcar asistencia (muestra modal con QR).
 */
public function generarQR(Horario $horario)
{
    $docente = Auth::user()->docente;

    // Verificar que sea el docente correcto
    if (!$docente || $horario->grupo->docente_id !== $docente->id) {
        abort(403, 'No autorizado para generar este código QR.');
    }

    // Generar URL firmada con expiración de 1 hora
    $signedUrl = URL::temporarySignedRoute(
        'asistencias.qr.scan',
        now()->addHour(),
        ['horario' => $horario->id, 'token' => encrypt($docente->id)]
    );

    // Generar el código QR en formato SVG
    $qrCode = QrCode::format('svg')
                    ->size(300)
                    ->margin(1)
                    ->generate($signedUrl);

    // Devolver vista con el QR
    return view('docente.qr-modal', [
        'horario' => $horario,
        'qrCode' => $qrCode,
    ]);
}

/**
 * Escanea el QR y marca asistencia automáticamente (ruta pública).
 */
public function escanearQR(Request $request, Horario $horario, $token)
{
    // 1. Validar firma de URL (si expiró o fue modificada, falla)
    if (!$request->hasValidSignature()) {
        return view('errors.qr-expired')->with('error', 'Este código QR ha expirado o no es válido.');
    }

    try {
        // 2. Desencriptar el ID del docente desde el token
        $docenteId = decrypt($token);
    } catch (\Exception $e) {
        return view('errors.qr-invalid')->with('error', 'Código QR inválido.');
    }

    // 3. Verificar que el docente corresponda al horario
    if ($horario->grupo->docente_id !== $docenteId) {
        return view('errors.qr-unauthorized')->with('error', 'No autorizado para marcar esta asistencia.');
    }

    // 4. Obtener datos actuales
    $now = Carbon::now();
    $todayDate = $now->toDateString();
    $currentTime = $now->format('H:i:s');

    // 5. Validar ventana de tiempo (-15/+15 min desde inicio)
    $margenMinutos = 15;
    $inicioVentana = Carbon::parse($horario->hora_inicio)->subMinutes($margenMinutos)->format('H:i:s');
    $finVentana    = Carbon::parse($horario->hora_inicio)->addMinutes($margenMinutos)->format('H:i:s');

    if ($currentTime < $inicioVentana || $currentTime > $finVentana) {
        return view('errors.qr-time-window')->with([
            'error' => 'No puede marcar asistencia fuera de la ventana permitida.',
            'ventana' => $inicioVentana . ' - ' . $finVentana
        ]);
    }

    // 6. Validar si ya marcó asistencia HOY para ESTA clase
    $alreadyMarked = Asistencia::where('horario_id', $horario->id)
                              ->where('docente_id', $docenteId)
                              ->where('fecha', $todayDate)
                              ->exists();

    if ($alreadyMarked) {
        return view('docente.qr-success')->with([
            'mensaje' => 'Ya ha marcado asistencia para esta clase hoy.',
            'tipo' => 'info',
            'horario' => $horario
        ]);
    }

    // 7. CREAR el registro
    Asistencia::create([
        'horario_id' => $horario->id,
        'docente_id' => $docenteId,
        'fecha' => $todayDate,
        'hora_registro' => $currentTime,
        'estado' => 'Presente',
        'metodo_registro' => 'QR',
    ]);

    // 8. Mostrar página de éxito
    return view('docente.qr-success')->with([
        'mensaje' => '¡Asistencia marcada exitosamente!',
        'tipo' => 'success',
        'horario' => $horario,
        'fecha' => $todayDate,
        'hora' => $currentTime
    ]);
}

}
