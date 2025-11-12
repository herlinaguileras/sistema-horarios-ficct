<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Materia;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Aula;
use App\Models\User;
use App\Models\Role;
use App\Models\Semestre;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;
use App\Traits\LogsActivity;

class HorarioImportController extends Controller
{
    use LogsActivity;
    /**
     * Mostrar formulario de importación
     */
    public function index()
    {
        return view('horarios.import');
    }

    /**
     * Procesar archivo de importación
     */
    public function import(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);

        DB::beginTransaction();

        try {
            $archivo = $request->file('archivo');
            $spreadsheet = IOFactory::load($archivo->getRealPath());
            $hoja = $spreadsheet->getActiveSheet();
            $filas = $hoja->toArray();

            // Eliminar encabezado
            array_shift($filas);

            $estadisticas = [
                'total' => 0,
                'exitosas' => 0,
                'errores' => 0,
                'docentes_creados' => 0,
                'materias_creadas' => 0,
                'grupos_creados' => 0,
                'aulas_creadas' => 0,
                'horarios_creados' => 0,
                'detalles' => []
            ];

            foreach ($filas as $numeroFila => $fila) {
                $numeroLinea = $numeroFila + 2; // +2 por el header y porque empieza en 1

                // Saltar filas vacías
                if (empty(array_filter($fila))) {
                    continue;
                }

                $estadisticas['total']++;

                try {
                    $resultado = $this->procesarFila($fila, $numeroLinea);

                    if ($resultado['exito']) {
                        $estadisticas['exitosas']++;
                        $estadisticas['docentes_creados'] += $resultado['docente_creado'] ? 1 : 0;
                        $estadisticas['materias_creadas'] += $resultado['materia_creada'] ? 1 : 0;
                        $estadisticas['grupos_creados'] += $resultado['grupo_creado'] ? 1 : 0;
                        $estadisticas['aulas_creadas'] += $resultado['aulas_creadas'];
                        $estadisticas['horarios_creados'] += $resultado['horarios_creados'];
                    } else {
                        $estadisticas['errores']++;
                    }

                    $estadisticas['detalles'][] = [
                        'linea' => $numeroLinea,
                        'exito' => $resultado['exito'],
                        'mensaje' => $resultado['mensaje'],
                        'advertencias' => $resultado['advertencias'] ?? [],
                        'errores_validacion' => $resultado['errores_validacion'] ?? []
                    ];

                } catch (Exception $e) {
                    $estadisticas['errores']++;
                    $estadisticas['detalles'][] = [
                        'linea' => $numeroLinea,
                        'exito' => false,
                        'mensaje' => 'Error: ' . $e->getMessage(),
                        'advertencias' => [],
                        'errores_validacion' => []
                    ];
                }
            }

            DB::commit();

            // Registrar importación exitosa en bitácora
            $this->logImport(null, [
                'total_filas' => $estadisticas['total'],
                'exitosas' => $estadisticas['exitosas'],
                'fallidas' => $estadisticas['fallidas'],
                'omitidas' => $estadisticas['omitidas'],
                'docentes_creados' => $estadisticas['docentes_creados'],
                'materias_creadas' => $estadisticas['materias_creadas'],
                'grupos_creados' => $estadisticas['grupos_creados'],
                'aulas_creadas' => $estadisticas['aulas_creadas'],
                'horarios_creados' => $estadisticas['horarios_creados'],
                'archivo' => $archivo->getClientOriginalName(),
            ]);

            return view('horarios.import-result', compact('estadisticas'));

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al procesar archivo: ' . $e->getMessage()]);
        }
    }

    /**
     * Procesar una fila del Excel
     * Formato: SIGLA | SEMESTRE | GRUPO | MATERIA | DOCENTE | DIA | HORA | AULA | DIA | HORA | AULA | ...
     */
    private function procesarFila($fila, $numeroLinea)
    {
        $resultado = [
            'exito' => false,
            'mensaje' => '',
            'advertencias' => [],
            'errores_validacion' => [],
            'docente_creado' => false,
            'materia_creada' => false,
            'grupo_creado' => false,
            'aulas_creadas' => 0,
            'horarios_creados' => 0
        ];

        // Extraer datos base (primeras 5 columnas)
        $sigla = trim($fila[0] ?? '');
        $semestreNum = trim($fila[1] ?? '');
        $nombreGrupo = trim($fila[2] ?? '');
        $nombreMateria = trim($fila[3] ?? '');
        $nombreDocente = trim($fila[4] ?? '');

        // Validar datos base
        if (empty($sigla) || empty($semestreNum) || empty($nombreGrupo) || empty($nombreMateria) || empty($nombreDocente)) {
            $resultado['mensaje'] = 'Faltan datos básicos (SIGLA, SEMESTRE, GRUPO, MATERIA o DOCENTE)';
            return $resultado;
        }

        // 1. Obtener semestre activo
        $semestre = Semestre::where('estado', 'Activo')->first();
        if (!$semestre) {
            $resultado['mensaje'] = 'No hay semestre activo en el sistema';
            return $resultado;
        }

        // 2. Crear o actualizar MATERIA
        $materia = Materia::where('sigla', $sigla)->first();
        if (!$materia) {
            // Extraer nivel del semestre del campo SEMESTRE (Ej: "1" o "Primer Semestre")
            $nivelSemestre = $this->extraerNivelSemestre($semestreNum);

            $materia = Materia::create([
                'sigla' => $sigla,
                'nombre' => $nombreMateria,
                'nivel_semestre' => $nivelSemestre
            ]);
            $resultado['materia_creada'] = true;
            $resultado['advertencias'][] = "✓ Materia creada: {$sigla} (Nivel {$nivelSemestre})";
        } else {
            // Actualizar nombre si es diferente
            if ($materia->nombre !== $nombreMateria) {
                $materia->update(['nombre' => $nombreMateria]);
                $resultado['advertencias'][] = "↻ Materia actualizada: {$sigla}";
            }
        }

        // 3. Crear o buscar DOCENTE
        $docente = $this->obtenerOCrearDocente($nombreDocente, $resultado);
        if (!$docente) {
            $resultado['mensaje'] = 'Error al crear/obtener docente';
            return $resultado;
        }

        // 4. Crear o actualizar GRUPO
        $grupo = Grupo::where('nombre', $nombreGrupo)
            ->where('materia_id', $materia->id)
            ->where('semestre_id', $semestre->id)
            ->first();

        if (!$grupo) {
            $grupo = Grupo::create([
                'nombre' => $nombreGrupo,
                'materia_id' => $materia->id,
                'docente_id' => $docente->id,
                'semestre_id' => $semestre->id
            ]);
            $resultado['grupo_creado'] = true;
            $resultado['advertencias'][] = "✓ Grupo creado: {$nombreGrupo}";
        } else {
            // Actualizar docente si es diferente
            if ($grupo->docente_id !== $docente->id) {
                $grupo->update(['docente_id' => $docente->id]);
                $resultado['advertencias'][] = "↻ Docente del grupo actualizado";
            }
        }

        // 5. ELIMINAR horarios anteriores del grupo
        Horario::where('grupo_id', $grupo->id)->delete();

        // 6. Procesar HORARIOS (a partir de la columna 5, en grupos de 3: DIA, HORA, AULA)
        $horariosCreados = 0;
        $horariosPendientes = []; // Array temporal para validar choques
        $indice = 5; // Empezar después de DOCENTE

        // PASO 1: Recopilar todos los horarios de esta fila
        while (isset($fila[$indice])) {
            $dia = trim($fila[$indice] ?? '');
            $hora = trim($fila[$indice + 1] ?? '');
            $aulaNumero = trim($fila[$indice + 2] ?? '');

            // Si las 3 columnas están vacías, terminar
            if (empty($dia) && empty($hora) && empty($aulaNumero)) {
                break;
            }

            // Validar que las 3 estén presentes
            if (empty($dia) || empty($hora) || empty($aulaNumero)) {
                $resultado['advertencias'][] = "⚠ Horario incompleto en columna " . ($indice + 1);
                $indice += 3;
                continue;
            }

            // Normalizar día
            $diaNormalizado = $this->normalizarDia($dia);
            if (!$diaNormalizado) {
                $resultado['advertencias'][] = "⚠ Día inválido: '{$dia}'";
                $indice += 3;
                continue;
            }

            // Parsear hora (formato: 18:15-20:30)
            $horas = $this->parsearHora($hora);
            if (!$horas) {
                $resultado['advertencias'][] = "⚠ Formato de hora inválido: '{$hora}'";
                $indice += 3;
                continue;
            }

            // Crear o buscar AULA
            $aula = Aula::where('nombre', $aulaNumero)->first();
            if (!$aula) {
                $aula = Aula::create([
                    'nombre' => $aulaNumero,
                    'capacidad' => 30,
                    'piso' => 1,
                    'tipo' => 'Aula'
                ]);
                $resultado['aulas_creadas']++;
                $resultado['advertencias'][] = "✓ Aula creada: {$aulaNumero}";
            }

            // Guardar horario pendiente para validación
            $horariosPendientes[] = [
                'aula' => $aula,
                'aula_numero' => $aulaNumero,
                'dia' => $diaNormalizado,
                'dia_nombre' => $dia,
                'hora_inicio' => $horas['inicio'],
                'hora_fin' => $horas['fin'],
                'hora_texto' => $hora
            ];

            $indice += 3;
        }

        // PASO 2: Validar choques ANTES de crear los horarios
        $tieneErrores = false;

        foreach ($horariosPendientes as $index => $horarioPendiente) {
            // Validación 1: Choque de AULA (misma aula ocupada en mismo horario)
            $choqueAula = $this->verificarChoqueAula(
                $horarioPendiente['aula']->id,
                $horarioPendiente['dia'],
                $horarioPendiente['hora_inicio'],
                $horarioPendiente['hora_fin'],
                $grupo->id,
                $grupo->semestre_id
            );

            if ($choqueAula) {
                $tieneErrores = true;
                $resultado['errores_validacion'][] = "❌ CHOQUE DE AULA: {$horarioPendiente['dia_nombre']} {$horarioPendiente['hora_texto']} - Aula {$horarioPendiente['aula_numero']} ya ocupada por {$choqueAula['materia']} - {$choqueAula['grupo']}";
            }

            // Validación 2: Choque de GRUPO (mismo grupo con otro horario simultáneo)
            $choqueGrupo = $this->verificarChoqueGrupo(
                $grupo->id,
                $horarioPendiente['dia'],
                $horarioPendiente['hora_inicio'],
                $horarioPendiente['hora_fin']
            );

            if ($choqueGrupo) {
                $tieneErrores = true;
                $resultado['errores_validacion'][] = "❌ CHOQUE DE GRUPO: {$horarioPendiente['dia_nombre']} {$horarioPendiente['hora_texto']} - El grupo {$nombreGrupo} ya tiene clase en Aula {$choqueGrupo['aula']}";
            }

            // Validación 3: Choque de DOCENTE (docente con otra clase al mismo tiempo)
            $choqueDocente = $this->verificarChoqueDocente(
                $docente->id,
                $horarioPendiente['dia'],
                $horarioPendiente['hora_inicio'],
                $horarioPendiente['hora_fin'],
                $grupo->id,
                $grupo->semestre_id
            );

            if ($choqueDocente) {
                $tieneErrores = true;
                $resultado['errores_validacion'][] = "❌ CHOQUE DE DOCENTE: {$horarioPendiente['dia_nombre']} {$horarioPendiente['hora_texto']} - {$nombreDocente} ya tiene clase con {$choqueDocente['materia']} - {$choqueDocente['grupo']} en Aula {$choqueDocente['aula']}";
            }

            // Validación 4: Choque INTERNO (dentro de la misma fila del Excel)
            for ($j = 0; $j < $index; $j++) {
                $otroHorario = $horariosPendientes[$j];

                // Mismo día
                if ($otroHorario['dia'] === $horarioPendiente['dia']) {
                    // Verificar solapamiento de horas
                    if ($this->horariosSeSuperponen(
                        $otroHorario['hora_inicio'], $otroHorario['hora_fin'],
                        $horarioPendiente['hora_inicio'], $horarioPendiente['hora_fin']
                    )) {
                        // Mismo grupo no puede tener dos clases al mismo tiempo
                        $tieneErrores = true;
                        $resultado['errores_validacion'][] = "❌ CHOQUE INTERNO: {$horarioPendiente['dia_nombre']} - El grupo tiene dos horarios simultáneos ({$otroHorario['hora_texto']} y {$horarioPendiente['hora_texto']})";

                        // Misma aula no puede estar ocupada dos veces
                        if ($otroHorario['aula']->id === $horarioPendiente['aula']->id) {
                            $resultado['errores_validacion'][] = "❌ CHOQUE INTERNO AULA: {$horarioPendiente['dia_nombre']} - Aula {$horarioPendiente['aula_numero']} asignada dos veces ({$otroHorario['hora_texto']} y {$horarioPendiente['hora_texto']})";
                        }
                    }
                }
            }
        }

        // Si hay errores de validación, no crear horarios
        if ($tieneErrores) {
            $resultado['mensaje'] = 'No se crearon horarios debido a conflictos detectados';
            return $resultado;
        }

        // PASO 3: Si pasan todas las validaciones, crear los horarios
        foreach ($horariosPendientes as $horarioPendiente) {
            Horario::create([
                'grupo_id' => $grupo->id,
                'aula_id' => $horarioPendiente['aula']->id,
                'dia_semana' => $horarioPendiente['dia'],
                'hora_inicio' => $horarioPendiente['hora_inicio'],
                'hora_fin' => $horarioPendiente['hora_fin']
            ]);

            $horariosCreados++;
        }

        if ($horariosCreados === 0) {
            $resultado['mensaje'] = 'No se creó ningún horario válido';
            return $resultado;
        }

        $resultado['horarios_creados'] = $horariosCreados;
        $resultado['exito'] = true;
        $resultado['mensaje'] = "{$sigla} - {$nombreGrupo}: {$horariosCreados} horario(s) creado(s)";

        return $resultado;
    }

    /**
     * Obtener o crear docente
     */
    private function obtenerOCrearDocente($nombreCompleto, &$resultado)
    {
        // Buscar docente por nombre
        $docente = Docente::whereHas('user', function($q) use ($nombreCompleto) {
            $q->where('name', $nombreCompleto);
        })->first();

        if ($docente) {
            return $docente;
        }

        // Crear nuevo docente
        try {
            // Generar email
            $palabras = explode(' ', $nombreCompleto);
            $apellidos = array_slice($palabras, 0, 2);
            $emailBase = strtolower(implode('.', $apellidos));
            $emailBase = $this->quitarAcentos($emailBase);

            $email = $emailBase . '@ficct.edu.bo';
            $contador = 1;
            while (User::where('email', $email)->exists()) {
                $email = $emailBase . $contador . '@ficct.edu.bo';
                $contador++;
            }

            // Crear usuario
            $user = User::create([
                'name' => $nombreCompleto,
                'email' => $email,
                'password' => bcrypt('password123')
            ]);

            // Asignar rol de docente
            $docenteRole = Role::where('name', 'docente')->first();
            if ($docenteRole) {
                $user->roles()->attach($docenteRole->id);
            }

            // Generar código siguiendo la secuencia existente (100, 101, 102...)
            $ultimoDocente = Docente::orderBy('codigo_docente', 'desc')->first();
            $nuevoCodigo = $ultimoDocente ? ((int)$ultimoDocente->codigo_docente + 1) : 100;

            // Crear docente
            $docente = Docente::create([
                'user_id' => $user->id,
                'codigo_docente' => (string)$nuevoCodigo,
                'carnet_identidad' => 'PENDIENTE'
            ]);

            $resultado['docente_creado'] = true;
            $resultado['advertencias'][] = "✓ Docente creado: {$nombreCompleto} (Código: {$nuevoCodigo})";

            return $docente;

        } catch (Exception $e) {
            Log::error("Error creando docente: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Normalizar día de la semana a número
     * 1 = Lunes, 2 = Martes, 3 = Miércoles, 4 = Jueves, 5 = Viernes, 6 = Sábado, 7 = Domingo
     */
    private function normalizarDia($dia)
    {
        $dias = [
            'lun' => 1,
            'lunes' => 1,
            'mar' => 2,
            'martes' => 2,
            'mie' => 3,
            'miercoles' => 3,
            'miércoles' => 3,
            'jue' => 4,
            'jueves' => 4,
            'vie' => 5,
            'viernes' => 5,
            'sab' => 6,
            'sabado' => 6,
            'sábado' => 6,
            'dom' => 7,
            'domingo' => 7
        ];

        return $dias[strtolower(trim($dia))] ?? null;
    }

    /**
     * Parsear hora en formato: 18:15-20:30
     */
    private function parsearHora($hora)
    {
        $hora = trim($hora);

        // Formato: 18:15-20:30 o 18:15 - 20:30
        if (preg_match('/^(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})$/', $hora, $matches)) {
            return [
                'inicio' => str_pad($matches[1], 5, '0', STR_PAD_LEFT),
                'fin' => str_pad($matches[2], 5, '0', STR_PAD_LEFT)
            ];
        }

        return null;
    }

    /**
     * Quitar acentos
     */
    private function quitarAcentos($texto)
    {
        $acentos = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u',
            'ñ' => 'n', 'Ñ' => 'n'
        ];
        return strtr($texto, $acentos);
    }

    /**
     * Extraer nivel de semestre del texto
     * Ejemplos: "1" => 1, "Primer Semestre" => 1, "2" => 2, etc.
     */
    private function extraerNivelSemestre($semestreTexto)
    {
        $texto = strtolower(trim($semestreTexto));

        // Si es un número directo
        if (is_numeric($texto)) {
            return (int) $texto;
        }

        // Mapeo de texto a número
        $niveles = [
            'primer' => 1, 'primero' => 1, '1er' => 1, '1°' => 1,
            'segundo' => 2, '2do' => 2, '2°' => 2,
            'tercer' => 3, 'tercero' => 3, '3er' => 3, '3°' => 3,
            'cuarto' => 4, '4to' => 4, '4°' => 4,
            'quinto' => 5, '5to' => 5, '5°' => 5,
            'sexto' => 6, '6to' => 6, '6°' => 6,
            'septimo' => 7, 'séptimo' => 7, '7mo' => 7, '7°' => 7,
            'octavo' => 8, '8vo' => 8, '8°' => 8,
            'noveno' => 9, '9no' => 9, '9°' => 9,
            'decimo' => 10, 'décimo' => 10, '10mo' => 10, '10°' => 10
        ];

        foreach ($niveles as $palabra => $numero) {
            if (strpos($texto, $palabra) !== false) {
                return $numero;
            }
        }

        // Por defecto, retornar 1
        return 1;
    }

    /**
     * Verificar si un aula está ocupada en un horario específico
     * Retorna información del choque o null si no hay conflicto
     */
    private function verificarChoqueAula($aulaId, $dia, $horaInicio, $horaFin, $grupoActualId, $semestreId)
    {
        $choque = Horario::where('aula_id', $aulaId)
            ->where('dia_semana', $dia)
            ->where('grupo_id', '!=', $grupoActualId) // Excluir el grupo actual
            ->whereHas('grupo', function($q) use ($semestreId) {
                $q->where('semestre_id', $semestreId); // Solo mismo semestre
            })
            ->where(function($query) use ($horaInicio, $horaFin) {
                // Verificar superposición de horarios
                $query->where(function($q) use ($horaInicio, $horaFin) {
                    // Caso 1: El horario existente empieza durante el nuevo horario
                    $q->whereBetween('hora_inicio', [$horaInicio, $horaFin]);
                })->orWhere(function($q) use ($horaInicio, $horaFin) {
                    // Caso 2: El horario existente termina durante el nuevo horario
                    $q->whereBetween('hora_fin', [$horaInicio, $horaFin]);
                })->orWhere(function($q) use ($horaInicio, $horaFin) {
                    // Caso 3: El horario existente envuelve completamente al nuevo
                    $q->where('hora_inicio', '<=', $horaInicio)
                      ->where('hora_fin', '>=', $horaFin);
                })->orWhere(function($q) use ($horaInicio, $horaFin) {
                    // Caso 4: El nuevo horario envuelve completamente al existente
                    $q->where('hora_inicio', '>=', $horaInicio)
                      ->where('hora_fin', '<=', $horaFin);
                });
            })
            ->with(['grupo.materia', 'grupo'])
            ->first();

        if ($choque) {
            return [
                'materia' => $choque->grupo->materia->sigla ?? 'N/A',
                'grupo' => $choque->grupo->nombre ?? 'N/A',
                'hora_inicio' => $choque->hora_inicio,
                'hora_fin' => $choque->hora_fin
            ];
        }

        return null;
    }

    /**
     * Verificar si un grupo ya tiene otro horario al mismo tiempo
     */
    private function verificarChoqueGrupo($grupoId, $dia, $horaInicio, $horaFin)
    {
        $choque = Horario::where('grupo_id', $grupoId)
            ->where('dia_semana', $dia)
            ->where(function($query) use ($horaInicio, $horaFin) {
                $query->where(function($q) use ($horaInicio, $horaFin) {
                    $q->whereBetween('hora_inicio', [$horaInicio, $horaFin]);
                })->orWhere(function($q) use ($horaInicio, $horaFin) {
                    $q->whereBetween('hora_fin', [$horaInicio, $horaFin]);
                })->orWhere(function($q) use ($horaInicio, $horaFin) {
                    $q->where('hora_inicio', '<=', $horaInicio)
                      ->where('hora_fin', '>=', $horaFin);
                })->orWhere(function($q) use ($horaInicio, $horaFin) {
                    $q->where('hora_inicio', '>=', $horaInicio)
                      ->where('hora_fin', '<=', $horaFin);
                });
            })
            ->with('aula')
            ->first();

        if ($choque) {
            return [
                'aula' => $choque->aula->nombre ?? 'N/A',
                'hora_inicio' => $choque->hora_inicio,
                'hora_fin' => $choque->hora_fin
            ];
        }

        return null;
    }

    /**
     * Verificar si un docente ya tiene otra clase al mismo tiempo
     */
    private function verificarChoqueDocente($docenteId, $dia, $horaInicio, $horaFin, $grupoActualId, $semestreId)
    {
        $choque = Horario::whereHas('grupo', function($q) use ($docenteId, $semestreId, $grupoActualId) {
                $q->where('docente_id', $docenteId)
                  ->where('semestre_id', $semestreId)
                  ->where('id', '!=', $grupoActualId); // Excluir el grupo actual
            })
            ->where('dia_semana', $dia)
            ->where(function($query) use ($horaInicio, $horaFin) {
                $query->where(function($q) use ($horaInicio, $horaFin) {
                    $q->whereBetween('hora_inicio', [$horaInicio, $horaFin]);
                })->orWhere(function($q) use ($horaInicio, $horaFin) {
                    $q->whereBetween('hora_fin', [$horaInicio, $horaFin]);
                })->orWhere(function($q) use ($horaInicio, $horaFin) {
                    $q->where('hora_inicio', '<=', $horaInicio)
                      ->where('hora_fin', '>=', $horaFin);
                })->orWhere(function($q) use ($horaInicio, $horaFin) {
                    $q->where('hora_inicio', '>=', $horaInicio)
                      ->where('hora_fin', '<=', $horaFin);
                });
            })
            ->with(['grupo.materia', 'grupo', 'aula'])
            ->first();

        if ($choque) {
            return [
                'materia' => $choque->grupo->materia->sigla ?? 'N/A',
                'grupo' => $choque->grupo->nombre ?? 'N/A',
                'aula' => $choque->aula->nombre ?? 'N/A',
                'hora_inicio' => $choque->hora_inicio,
                'hora_fin' => $choque->hora_fin
            ];
        }

        return null;
    }

    /**
     * Verificar si dos rangos de horarios se superponen
     */
    private function horariosSeSuperponen($inicio1, $fin1, $inicio2, $fin2)
    {
        // Convertir a timestamp para comparar
        $i1 = strtotime($inicio1);
        $f1 = strtotime($fin1);
        $i2 = strtotime($inicio2);
        $f2 = strtotime($fin2);

        // Se superponen si:
        // - El horario 2 empieza antes de que termine el horario 1 Y
        // - El horario 2 termina después de que empiece el horario 1
        return ($i2 < $f1) && ($f2 > $i1);
    }

    /**
     * Descargar plantilla de ejemplo
     */
    public function descargarPlantilla()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $headers = ['SIGLA', 'SEMESTRE', 'GRUPO', 'MATERIA', 'DOCENTE',
                    'DIA', 'HORA', 'AULA', 'DIA', 'HORA', 'AULA',
                    'DIA', 'HORA', 'AULA', 'DIA', 'HORA', 'AULA'];
        $sheet->fromArray($headers, null, 'A1');

        // Ejemplos
        $ejemplos = [
            ['MAT101', '1', 'F1', 'CALCULO I', 'AVENDAÑO GONZALES EUDAL', 'Mar', '18:15-20:30', '14', 'Jue', '18:15-20:30', '14', 'Vie', '9:15-11:30', '12', 'Jue', '9:15-11:30', '12'],
            ['FIS100', '1', 'A', 'FISICA I', 'RODRIGUEZ PEREZ MARIO', 'Lun', '08:00-10:00', '201', 'Mie', '08:00-10:00', '201', '', '', '', '', '', ''],
            ['QUI150', '2', 'B', 'QUIMICA GENERAL', 'LOPEZ SANTOS ANA', 'Mar', '14:00-16:00', '305', 'Jue', '14:00-16:00', '305', 'Vie', '14:00-16:00', '305', '', '', ''],
        ];
        $sheet->fromArray($ejemplos, null, 'A2');

        // Estilos
        $sheet->getStyle('A1:Q1')->getFont()->setBold(true);
        $sheet->getStyle('A1:Q1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF4CAF50');

        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $temp = tempnam(sys_get_temp_dir(), 'plantilla_horarios');
        $writer->save($temp);

        return response()->download($temp, 'plantilla_importacion_horarios.xlsx')->deleteFileAfterSend(true);
    }
}
