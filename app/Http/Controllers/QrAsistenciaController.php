<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Asistencia;
use App\Models\Horario;
use Illuminate\Http\Request;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QrAsistenciaController extends Controller
{
    /**
     * Muestra la vista para escanear códigos QR (Admin/Secretaria)
     */
    public function showScanner()
    {
        return view('asistencia.escanear-qr');
    }

    /**
     * Muestra el QR del docente autenticado y lista de clases del día.
     * El docente puede marcar su propia asistencia (self-service).
     */
    public function showMyQr()
    {
        $user = Auth::user();

        // Verificar que el usuario tenga perfil de docente
        $docente = Docente::where('user_id', $user->id)->first();

        if (!$docente) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes un perfil de docente asignado.');
        }

        // Generar datos del QR (incluye timestamp actual y firma)
        $qrData = $docente->generateQrData();

        // Generar código QR visual
        $qrCode = QrCode::size(300)
            ->margin(1)
            ->generate($qrData);

        return view('asistencia.mi-qr', [
            'docente' => $docente,
            'qrCode' => $qrCode,
            'qrData' => $qrData,
        ]);
    }

    /**
     * Permite al docente marcar su propia asistencia para una clase específica.
     * Self-service con validación de horario y ventana de tiempo.
     */
    public function marcarMiAsistencia(Request $request, Horario $horario)
    {
        try {
            $user = Auth::user();
            $docente = Docente::where('user_id', $user->id)->first();

            if (!$docente) {
                return back()->with('error', 'No tienes un perfil de docente asignado.');
            }

            // Cargar la relación grupo para acceder al docente_id
            $horario->load('grupo.materia');

            // Verificar que el horario pertenece a este docente (a través del grupo)
            if ($horario->grupo->docente_id !== $docente->id) {
                return back()->with('error', 'No estás autorizado para marcar asistencia en esta clase.');
            }

            $now = Carbon::now();
            $hoy = $now->toDateString();

            // Verificar que sea el día correcto (usar dayOfWeekIso: lunes=1, domingo=7)
            if ($now->dayOfWeekIso !== $horario->dia_semana) {
                return back()->with('error', 'Esta clase no corresponde al día de hoy. Hoy es ' . $now->locale('es')->dayName . ' y la clase es el día ' . $horario->dia_semana);
            }

            // Verificar ventana de tiempo (15 min antes del inicio hasta 15 min después del inicio)
            // Crear objetos Carbon con la fecha y hora actual para comparación precisa
            $horaInicio = Carbon::parse($hoy . ' ' . $horario->hora_inicio);
            $ventanaInicio = $horaInicio->copy()->subMinutes(15);
            $ventanaFin = $horaInicio->copy()->addMinutes(15);

            // Usar betweenIncluded para incluir los límites de la ventana
            if (!$now->betweenIncluded($ventanaInicio, $ventanaFin)) {
                return back()->with('error',
                    'Fuera de la ventana de registro. Puedes marcar desde ' .
                    $ventanaInicio->format('H:i') . ' hasta ' . $ventanaFin->format('H:i') .
                    ' (Hora actual: ' . $now->format('H:i') . ')'
                );
            }

            // Verificar si ya registró asistencia hoy para esta clase
            $asistenciaExistente = Asistencia::where('horario_id', $horario->id)
                ->where('docente_id', $docente->id)
                ->whereDate('fecha', $hoy)
                ->first();

            if ($asistenciaExistente) {
                return back()->with('info',
                    'Ya registraste asistencia para esta clase hoy a las ' .
                    Carbon::parse($asistenciaExistente->hora_registro)->format('H:i')
                );
            }

            // Determinar si llegó a tiempo (tolerancia 15 min)
            $horaRegistro = $now->format('H:i:s');
            $tolerancia = 15;
            $llegaTarde = $now->greaterThan($horaInicio->addMinutes($tolerancia));

            // Registrar asistencia
            Asistencia::create([
                'docente_id' => $docente->id,
                'horario_id' => $horario->id,
                'fecha' => $hoy,
                'hora_registro' => $horaRegistro,
                'estado' => $llegaTarde ? Asistencia::ESTADO_TARDANZA : Asistencia::ESTADO_PRESENTE,
                'metodo_registro' => Asistencia::METODO_QR,
                'observaciones' => 'Registrado por el docente',
                'registrado_por' => $user->id,
            ]);

            $mensaje = '✅ Asistencia registrada exitosamente para ' . $horario->grupo->materia->nombre;
            if ($llegaTarde) {
                $mensaje .= ' (Registrada como TARDANZA)';
            }

            return back()->with('success', $mensaje);

        } catch (\Exception $e) {
            \Log::error('Error al marcar asistencia QR: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'horario_id' => $horario->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Ocurrió un error al registrar la asistencia. Por favor, intenta nuevamente o contacta al administrador.');
        }
    }

        /**
     * Procesa el código QR escaneado y redirige a selección de clase.
     * Paso 1 del flujo QR: Validar QR del docente
     */
    public function processQr(Request $request)
    {
        try {
            $request->validate([
                'qr_data' => 'required|string',
            ]);

            $qrData = $request->qr_data;

            // Validar QR usando el método del modelo
            $validation = Docente::validateQrData($qrData, 5);

            if (!$validation['valid']) {
                return back()->with('error', $validation['message']);
            }

            $docente = $validation['data']['docente'];
            $now = Carbon::now();
            $diaActual = $now->dayOfWeekIso; // 1=Lunes, 2=Martes, ..., 7=Domingo (ISO 8601)

            // Buscar horarios del docente para el día actual
            $horarios = Horario::whereHas('grupo', function($query) use ($docente) {
                $query->where('docente_id', $docente->id);
            })
            ->where('dia_semana', $diaActual)
            ->with(['grupo.materia', 'aula'])
            ->orderBy('hora_inicio')
            ->get();

            if ($horarios->isEmpty()) {
                return back()->with('warning', 'El docente ' . $docente->user->name . ' no tiene clases programadas para hoy.');
            }

            // Redirigir a página de selección de clase
            return redirect()->route('asistencia.qr.seleccionar-clase', ['docente' => $docente->id])
                ->with('qr_validated', true)
                ->with('qr_timestamp', $validation['data']['timestamp']->timestamp);

        } catch (\Exception $e) {
            Log::error('Error al procesar QR: ' . $e->getMessage(), [
                'qr_data' => $request->qr_data ?? null,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error al procesar el código QR. Verifica que el formato sea correcto.');
        }
    }

    /**
     * MÉTODO ANTIGUO - Mantener por compatibilidad
     * Procesa el código QR escaneado o ingresado manualmente (método directo original)
     */
    public function processQrDirecto(Request $request)
    {
        try {
            $request->validate([
                'qr_data' => 'required|string',
            ]);

            $qrData = $request->qr_data;

            // Decodificar datos del QR
            $data = json_decode($qrData, true);

            if (!$data || !isset($data['docente_id'], $data['codigo_docente'], $data['timestamp'], $data['signature'])) {
                return back()->with('error', 'Código QR inválido o mal formado.');
            }

            // Buscar el docente por código
            $docente = Docente::find($data['docente_id']);

            if (!$docente || $docente->codigo_docente !== $data['codigo_docente']) {
                return back()->with('error', 'Docente no encontrado.');
            }

            // Validar la firma del QR
            if (!$docente->validateQrSignature($data)) {
                return back()->with('error', 'Código QR inválido o expirado. Solicita uno nuevo.');

            }

            $now = Carbon::now();
            $hoy = $now->toDateString();
            $diaActual = $now->dayOfWeekIso; // 1=Lunes, 2=Martes, ..., 7=Domingo (ISO 8601)

            // Buscar horarios del docente para el día actual
            $horarios = Horario::whereHas('grupo', function($query) use ($docente) {
                $query->where('docente_id', $docente->id);
            })->where('dia_semana', $diaActual)->get();

            if ($horarios->isEmpty()) {
                return back()->with('warning', 'El docente ' . $docente->user->name . ' no tiene clases programadas para hoy.');
            }

            // Encontrar el horario más cercano a la hora actual
            $horarioActual = null;
            $menorDiferencia = PHP_INT_MAX;

            foreach ($horarios as $horario) {
                $horaInicio = Carbon::parse($hoy . ' ' . $horario->hora_inicio);
                $ventanaInicio = $horaInicio->copy()->subMinutes(15);
                $ventanaFin = $horaInicio->copy()->addMinutes(15);

                // Usar betweenIncluded para incluir los límites
                if ($now->betweenIncluded($ventanaInicio, $ventanaFin)) {
                    $horarioActual = $horario;
                    break;
                }
            }

            if (!$horarioActual) {
                return back()->with('warning',
                    'No hay clases del docente ' . $docente->user->name . ' en este momento (hora actual: ' . $now->format('H:i') . '). ' .
                    'La asistencia solo puede registrarse dentro de la ventana de tiempo permitida (15 min antes y 15 min después del inicio de clase).'
                );
            }

            // Verificar si ya registró asistencia hoy para esta clase
            $asistenciaExistente = Asistencia::where('horario_id', $horarioActual->id)
                ->where('docente_id', $docente->id)
                ->whereDate('fecha', $hoy)
                ->first();

            if ($asistenciaExistente) {
                return back()->with('info',
                    'El docente ' . $docente->user->name . ' ya registró asistencia para ' .
                    $horarioActual->grupo->materia->nombre . ' hoy a las ' .
                    Carbon::parse($asistenciaExistente->hora_registro)->format('H:i')
                );
            }

            // Determinar si llegó a tiempo
            $horaInicio = Carbon::parse($horarioActual->hora_inicio);
            $horaRegistro = $now->format('H:i:s');
            $tolerancia = 15;
            $llegaTarde = $now->greaterThan($horaInicio->addMinutes($tolerancia));

            // Registrar asistencia
            Asistencia::create([
                'docente_id' => $docente->id,
                'horario_id' => $horarioActual->id,
                'fecha' => $hoy,
                'hora_registro' => $horaRegistro,
                'estado' => $llegaTarde ? Asistencia::ESTADO_TARDANZA : Asistencia::ESTADO_PRESENTE,
                'metodo_registro' => Asistencia::METODO_QR,
                'observaciones' => 'Registrado mediante escaneo QR',
                'registrado_por' => Auth::id(),
            ]);

            $mensaje = '✅ Asistencia registrada exitosamente para ' . $docente->user->name .
                      ' en ' . $horarioActual->grupo->materia->nombre;
            if ($llegaTarde) {
                $mensaje .= ' (Registrada como TARDANZA)';
            }

            return back()->with('success', $mensaje);

        } catch (\Exception $e) {
            \Log::error('Error al procesar QR: ' . $e->getMessage(), [
                'qr_data' => $request->qr_data ?? null,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error al procesar el código QR. Verifica que el formato sea correcto.');
        }
    }

    /**
     * Valida un código QR mediante AJAX (para escaneo en tiempo real)
     */
    public function validateQr(Request $request)
    {
        try {
            $request->validate([
                'qr_data' => 'required|string',
            ]);

            $qrData = $request->qr_data;

            // Decodificar datos del QR
            $data = json_decode($qrData, true);

            if (!$data || !isset($data['codigo_docente'], $data['timestamp'], $data['signature'])) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Código QR inválido o mal formado.'
                ]);
            }

            // Buscar el docente por código
            $docente = Docente::where('codigo_docente', $data['codigo_docente'])->first();

            if (!$docente) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Docente no encontrado.'
                ]);
            }

            // Validar la firma del QR
            if (!$docente->validateQrSignature($data)) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Código QR inválido o expirado.'
                ]);
            }

            return response()->json([
                'valid' => true,
                'message' => 'Código QR válido',
                'docente' => [
                    'nombre' => $docente->user->name,
                    'codigo' => $docente->codigo_docente,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al validar QR: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'message' => 'Error al validar el código QR.'
            ], 500);
        }
    }

    /**
     * Muestra las clases del día del docente para seleccionar cuál marcar.
     * Paso 2 del flujo QR: Selección de clase después de validar QR
     */
    public function mostrarClasesDocente(Docente $docente)
    {
        // Verificar que venimos de un QR validado
        if (!session('qr_validated')) {
            return redirect()->route('asistencia.qr.escanear')
                ->with('error', 'Primero debes escanear un código QR válido.');
        }

        $now = Carbon::now();
        $hoy = $now->toDateString();
        $diaActual = $now->dayOfWeekIso;

        // Obtener horarios del docente para hoy
        $horarios = Horario::whereHas('grupo', function($query) use ($docente) {
                $query->where('docente_id', $docente->id);
            })
            ->where('dia_semana', $diaActual)
            ->with(['grupo.materia', 'aula'])
            ->orderBy('hora_inicio')
            ->get();

        // Calcular información adicional para cada horario
        $horariosConInfo = $horarios->map(function($horario) use ($now, $hoy, $docente) {
            $horaInicio = Carbon::parse($hoy . ' ' . $horario->hora_inicio);
            $horaFin = Carbon::parse($hoy . ' ' . $horario->hora_fin);
            $ventanaInicio = $horaInicio->copy()->subMinutes(15);
            $ventanaFin = $horaInicio->copy()->addMinutes(15);

            $enVentana = $now->betweenIncluded($ventanaInicio, $ventanaFin);
            $yaTermino = $now->greaterThan($horaFin);

            // Verificar si ya marcó asistencia
            $asistencia = Asistencia::where('horario_id', $horario->id)
                ->where('docente_id', $docente->id)
                ->whereDate('fecha', $hoy)
                ->first();

            return [
                'horario' => $horario,
                'en_ventana' => $enVentana,
                'ya_termino' => $yaTermino,
                'asistencia' => $asistencia,
                'ventana_inicio' => $ventanaInicio,
                'ventana_fin' => $ventanaFin,
            ];
        });

        return view('asistencia.seleccionar-clase', [
            'docente' => $docente,
            'horariosConInfo' => $horariosConInfo,
        ]);
    }

    /**
     * Marca asistencia del docente para una clase específica después de validar QR.
     * Paso 3 del flujo QR: Registrar asistencia de la clase seleccionada
     */
    public function marcarAsistenciaQr(Docente $docente, Horario $horario)
    {
        try {
            // Verificar que venimos de un QR validado
            if (!session('qr_validated')) {
                return redirect()->route('asistencia.qr.escanear')
                    ->with('error', 'Primero debes escanear un código QR válido.');
            }

            // Verificar que el horario pertenece al docente
            if ($horario->grupo->docente_id !== $docente->id) {
                return back()->with('error', 'Este horario no pertenece al docente.');
            }

            $now = Carbon::now();
            $hoy = $now->toDateString();

            // Verificar que sea el día correcto
            if ($now->dayOfWeekIso !== $horario->dia_semana) {
                return back()->with('error', 'Esta clase no corresponde al día de hoy.');
            }

            // Verificar ventana de tiempo (15 min antes del inicio hasta 15 min después del inicio)
            $horaInicio = Carbon::parse($hoy . ' ' . $horario->hora_inicio);
            $ventanaInicio = $horaInicio->copy()->subMinutes(15);
            $ventanaFin = $horaInicio->copy()->addMinutes(15);

            if (!$now->betweenIncluded($ventanaInicio, $ventanaFin)) {
                return back()->with('error',
                    'Fuera de la ventana de registro. Puedes marcar desde ' .
                    $ventanaInicio->format('H:i') . ' hasta ' . $ventanaFin->format('H:i') .
                    ' (Hora actual: ' . $now->format('H:i') . ')'
                );
            }

            // Verificar si ya registró asistencia hoy para esta clase
            $asistenciaExistente = Asistencia::where('horario_id', $horario->id)
                ->where('docente_id', $docente->id)
                ->whereDate('fecha', $hoy)
                ->first();

            if ($asistenciaExistente) {
                return back()->with('info',
                    'Ya registraste asistencia para ' .
                    $horario->grupo->materia->nombre . ' hoy a las ' .
                    Carbon::parse($asistenciaExistente->hora_registro)->format('H:i')
                );
            }

            // Determinar si llegó a tiempo
            $horaRegistro = $now->format('H:i:s');
            $tolerancia = 15;
            $llegaTarde = $now->greaterThan($horaInicio->copy()->addMinutes($tolerancia));

            // Registrar asistencia
            Asistencia::create([
                'docente_id' => $docente->id,
                'horario_id' => $horario->id,
                'fecha' => $hoy,
                'hora_registro' => $horaRegistro,
                'estado' => $llegaTarde ? Asistencia::ESTADO_TARDANZA : Asistencia::ESTADO_PRESENTE,
                'metodo_registro' => Asistencia::METODO_QR,
                'observaciones' => 'Registrado mediante escaneo QR',
                'registrado_por' => Auth::id(),
            ]);

            $mensaje = '✅ Asistencia registrada exitosamente para ' .
                      $horario->grupo->materia->nombre . ' - Grupo ' . $horario->grupo->nombre;
            if ($llegaTarde) {
                $mensaje .= ' (Registrada como TARDANZA)';
            }

            // Limpiar sesión
            session()->forget(['qr_validated', 'qr_timestamp']);

            return redirect()->route('asistencia.qr.escanear')->with('success', $mensaje);

        } catch (\Exception $e) {
            \Log::error('Error al marcar asistencia QR: ' . $e->getMessage(), [
                'docente_id' => $docente->id,
                'horario_id' => $horario->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error al registrar la asistencia. Intenta nuevamente.');
        }
    }

    /**
     * Obtiene las clases del docente para hoy (vía AJAX)
     * Se usa cuando se escanea el QR en la pantalla del escáner
     */
    public function obtenerClasesDocente(Request $request)
    {
        try {
            $request->validate([
                'qr_data' => 'required|string',
            ]);

            $qrData = $request->qr_data;

            // Validar QR usando el método del modelo
            $validation = Docente::validateQrData($qrData, 5);

            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $validation['message']
                ]);
            }

            $docente = $validation['data']['docente'];
            $now = Carbon::now();
            $hoy = $now->toDateString();
            $diaActual = $now->dayOfWeekIso;

            // Obtener horarios del docente para hoy
            $horarios = Horario::whereHas('grupo', function($query) use ($docente) {
                    $query->where('docente_id', $docente->id);
                })
                ->where('dia_semana', $diaActual)
                ->with(['grupo.materia', 'aula'])
                ->orderBy('hora_inicio')
                ->get();

            // Preparar datos de las clases
            $clases = $horarios->map(function($horario) use ($now, $hoy, $docente) {
                $horaInicio = Carbon::parse($hoy . ' ' . $horario->hora_inicio);
                $horaFin = Carbon::parse($hoy . ' ' . $horario->hora_fin);
                $ventanaInicio = $horaInicio->copy()->subMinutes(15);
                $ventanaFin = $horaInicio->copy()->addMinutes(15);

                $enVentana = $now->betweenIncluded($ventanaInicio, $ventanaFin);

                // Verificar si ya marcó asistencia
                $asistencia = Asistencia::where('horario_id', $horario->id)
                    ->where('docente_id', $docente->id)
                    ->whereDate('fecha', $hoy)
                    ->first();

                return [
                    'horario_id' => $horario->id,
                    'materia' => $horario->grupo->materia->nombre,
                    'sigla' => $horario->grupo->materia->sigla,
                    'grupo' => $horario->grupo->nombre,
                    'aula' => $horario->aula->nombre,
                    'hora_inicio' => Carbon::parse($horario->hora_inicio)->format('H:i'),
                    'hora_fin' => Carbon::parse($horario->hora_fin)->format('H:i'),
                    'ventana_inicio' => $ventanaInicio->format('H:i'),
                    'ventana_fin' => $ventanaFin->format('H:i'),
                    'en_ventana' => $enVentana,
                    'asistencia_registrada' => $asistencia !== null,
                    'hora_registro' => $asistencia ? Carbon::parse($asistencia->hora_registro)->format('H:i') : null,
                ];
            });

            return response()->json([
                'success' => true,
                'docente' => [
                    'id' => $docente->id,
                    'nombre' => $docente->user->name,
                    'codigo' => $docente->codigo_docente,
                ],
                'clases' => $clases
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener clases del docente: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar las clases del docente.'
            ], 500);
        }
    }

    /**
     * Marca asistencia directamente vía AJAX desde el escáner
     */
    public function marcarAsistenciaDirecta(Request $request)
    {
        try {
            $request->validate([
                'docente_id' => 'required|integer',
                'horario_id' => 'required|integer',
            ]);

            $docente = Docente::findOrFail($request->docente_id);
            $horario = Horario::findOrFail($request->horario_id);

            // Verificar que el horario pertenece al docente
            if ($horario->grupo->docente_id !== $docente->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este horario no pertenece al docente.'
                ]);
            }

            $now = Carbon::now();
            $hoy = $now->toDateString();

            // Verificar que sea el día correcto
            if ($now->dayOfWeekIso !== $horario->dia_semana) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta clase no corresponde al día de hoy.'
                ]);
            }

            // Verificar ventana de tiempo
            $horaInicio = Carbon::parse($hoy . ' ' . $horario->hora_inicio);
            $ventanaInicio = $horaInicio->copy()->subMinutes(15);
            $ventanaFin = $horaInicio->copy()->addMinutes(15);

            if (!$now->betweenIncluded($ventanaInicio, $ventanaFin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fuera de la ventana de registro (' . $ventanaInicio->format('H:i') . ' - ' . $ventanaFin->format('H:i') . ')'
                ]);
            }

            // Verificar si ya registró asistencia
            $asistenciaExistente = Asistencia::where('horario_id', $horario->id)
                ->where('docente_id', $docente->id)
                ->whereDate('fecha', $hoy)
                ->first();

            if ($asistenciaExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya se registró asistencia para esta clase hoy.'
                ]);
            }

            // Determinar si llegó a tiempo
            $horaRegistro = $now->format('H:i:s');
            $tolerancia = 15;
            $llegaTarde = $now->greaterThan($horaInicio->copy()->addMinutes($tolerancia));

            // Registrar asistencia
            Asistencia::create([
                'docente_id' => $docente->id,
                'horario_id' => $horario->id,
                'fecha' => $hoy,
                'hora_registro' => $horaRegistro,
                'estado' => $llegaTarde ? Asistencia::ESTADO_TARDANZA : Asistencia::ESTADO_PRESENTE,
                'metodo_registro' => Asistencia::METODO_QR,
                'observaciones' => 'Registrado mediante escaneo QR',
                'registrado_por' => Auth::id(),
            ]);

            $mensaje = 'Asistencia registrada exitosamente para ' .
                      $horario->grupo->materia->nombre . ' - Grupo ' . $horario->grupo->nombre;
            
            if ($llegaTarde) {
                $mensaje .= ' (TARDANZA)';
            }

            return response()->json([
                'success' => true,
                'message' => $mensaje
            ]);

        } catch (\Exception $e) {
            Log::error('Error al marcar asistencia directa: ' . $e->getMessage(), [
                'docente_id' => $request->docente_id ?? null,
                'horario_id' => $request->horario_id ?? null,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la asistencia. Intenta nuevamente.'
            ], 500);
        }
    }
}

