@if ($semestreActivo)
    <div class="mb-4">
        <h4 class="mb-4 font-medium text-md">Tu Horario Semanal - {{ $semestreActivo->nombre }}</h4>

        @if($horariosDocente->isEmpty())
            <p class="text-gray-500">No tienes horarios asignados para este semestre.</p>
        @else
            {{-- Calendario Visual de Horarios --}}
            <div class="overflow-x-auto border rounded-lg shadow-sm">
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr class="bg-gradient-to-r from-indigo-600 to-indigo-500">
                            <th class="px-3 py-3 text-xs font-bold tracking-wider text-left text-white uppercase border-r border-indigo-400 w-28">
                                HORARIO
                            </th>
                            @foreach ($diasSemana as $numDia => $nombreDia)
                                <th class="px-3 py-3 text-xs font-bold tracking-wider text-center text-white uppercase border-r border-indigo-400 last:border-r-0">
                                    {{ strtoupper(substr($nombreDia, 0, 3)) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Generar todos los bloques horarios de 45 minutos desde 07:00 hasta 23:45
                            $horaInicio = 7 * 60; // 07:00 en minutos
                            $horaFin = 24 * 60; // 24:00 en minutos
                            $intervalo = 45; // minutos
                            $bloques = [];
                            
                            for ($minutos = $horaInicio; $minutos < $horaFin; $minutos += $intervalo) {
                                $horas = floor($minutos / 60);
                                $mins = $minutos % 60;
                                $inicioBloque = sprintf('%02d:%02d', $horas, $mins);
                                
                                $minutosFinBloque = $minutos + $intervalo;
                                $horasFinBloque = floor($minutosFinBloque / 60);
                                $minsFinBloque = $minutosFinBloque % 60;
                                $finBloque = sprintf('%02d:%02d', $horasFinBloque, $minsFinBloque);
                                
                                $bloques[] = [
                                    'inicio' => $inicioBloque,
                                    'fin' => $finBloque,
                                    'inicio_min' => $minutos,
                                    'fin_min' => $minutosFinBloque
                                ];
                            }
                            
                            // Organizar horarios por día y calcular su posición en bloques
                            $horariosPorDiaYBloque = [];
                            $bloquesOcupados = []; // Para rastrear qué bloques tienen clases
                            
                            foreach ($horariosDocente as $horario) {
                                $diaNum = $horario->dia_semana;
                                
                                // Convertir hora_inicio y hora_fin a minutos desde medianoche
                                $horaInicioPartes = explode(':', $horario->hora_inicio);
                                $horarioInicioMin = (int)$horaInicioPartes[0] * 60 + (int)$horaInicioPartes[1];
                                
                                $horaFinPartes = explode(':', $horario->hora_fin);
                                $horarioFinMin = (int)$horaFinPartes[0] * 60 + (int)$horaFinPartes[1];
                                
                                // Encontrar bloques que intersectan con este horario
                                foreach ($bloques as $indiceBloque => $bloque) {
                                    // Si el bloque se solapa con el horario
                                    if ($bloque['inicio_min'] < $horarioFinMin && $bloque['fin_min'] > $horarioInicioMin) {
                                        if (!isset($horariosPorDiaYBloque[$indiceBloque][$diaNum])) {
                                            $horariosPorDiaYBloque[$indiceBloque][$diaNum] = [];
                                        }
                                        $horariosPorDiaYBloque[$indiceBloque][$diaNum][] = $horario;
                                        $bloquesOcupados[$indiceBloque] = true; // Marcar bloque como ocupado
                                    }
                                }
                            }
                            
                            // Definir colores para diferentes materias
                            $colores = [
                                'bg-green-100 border-green-400 text-green-900',
                                'bg-pink-100 border-pink-400 text-pink-900',
                                'bg-cyan-100 border-cyan-400 text-cyan-900',
                                'bg-yellow-100 border-yellow-400 text-yellow-900',
                                'bg-orange-100 border-orange-400 text-orange-900',
                                'bg-purple-100 border-purple-400 text-purple-900',
                                'bg-blue-100 border-blue-400 text-blue-900',
                                'bg-red-100 border-red-400 text-red-900',
                            ];
                            
                            $materiaColores = [];
                            $colorIndex = 0;
                        @endphp

                        @foreach ($bloques as $indiceBloque => $bloque)
                            {{-- Solo mostrar bloques que tienen clases asignadas --}}
                            @if (isset($bloquesOcupados[$indiceBloque]))
                                <tr class="hover:bg-gray-50 {{ $indiceBloque % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                    {{-- Columna de hora --}}
                                    <td class="px-3 py-2 text-xs font-semibold text-gray-700 border-r border-b border-gray-200 whitespace-nowrap">
                                        {{ $bloque['inicio'] }} - {{ $bloque['fin'] }}
                                    </td>

                                    {{-- Columnas de días --}}
                                    @foreach ($diasSemana as $numDia => $nombreDia)
                                        <td class="relative px-2 py-2 text-center border-r border-b border-gray-200 last:border-r-0">
                                            @if (isset($horariosPorDiaYBloque[$indiceBloque][$numDia]))
                                                @foreach ($horariosPorDiaYBloque[$indiceBloque][$numDia] as $horario)
                                                    @php
                                                        // Asignar color único a cada materia
                                                        $materiaId = $horario->grupo->materia->id;
                                                        if (!isset($materiaColores[$materiaId])) {
                                                            $materiaColores[$materiaId] = $colores[$colorIndex % count($colores)];
                                                            $colorIndex++;
                                                        }
                                                        $colorClase = $materiaColores[$materiaId];
                                                        
                                                        // Calcular si este es el primer bloque del horario
                                                        $horaInicioPartes = explode(':', $horario->hora_inicio);
                                                        $horarioInicioMin = (int)$horaInicioPartes[0] * 60 + (int)$horaInicioPartes[1];
                                                        $esPrimerBloque = $bloque['inicio_min'] <= $horarioInicioMin && $bloque['fin_min'] > $horarioInicioMin;
                                                    @endphp
                                                    
                                                    <div class="px-2 py-1 mb-1 text-xs font-semibold border-l-4 rounded {{ $colorClase }}">
                                                        <div class="font-bold">{{ $horario->grupo->materia->sigla }} - {{ $horario->grupo->nombre }}</div>
                                                        <div class="text-xs opacity-90">{{ $horario->aula->nombre }}</div>
                                                        @if ($esPrimerBloque)
                                                            <div class="text-xs font-normal opacity-75 mt-1">
                                                                {{ $horario->grupo->materia->nombre }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-gray-300">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>            {{-- Leyenda de Materias --}}
            <div class="p-4 mt-4 border rounded-lg bg-gray-50">
                <h5 class="mb-3 text-sm font-semibold text-gray-700">Leyenda de Materias:</h5>
                <div class="space-y-2">
                    @php
                        // Obtener información completa de cada materia con sus horarios
                        $materiasConInfo = [];
                        foreach ($horariosDocente as $horario) {
                            $materiaId = $horario->grupo->materia->id;
                            if (!isset($materiasConInfo[$materiaId])) {
                                $materiasConInfo[$materiaId] = [
                                    'materia' => $horario->grupo->materia,
                                    'grupos' => [],
                                    'horarios' => []
                                ];
                            }
                            
                            // Agregar grupo si no existe
                            $grupoNombre = $horario->grupo->nombre;
                            if (!in_array($grupoNombre, $materiasConInfo[$materiaId]['grupos'])) {
                                $materiasConInfo[$materiaId]['grupos'][] = $grupoNombre;
                            }
                            
                            // Agregar horario
                            $diaNum = $horario->dia_semana;
                            $horarioTexto = $diasSemana[$diaNum] . ' ' . 
                                          date('H:i', strtotime($horario->hora_inicio)) . ' - ' . 
                                          date('H:i', strtotime($horario->hora_fin));
                            
                            if (!in_array($horarioTexto, $materiasConInfo[$materiaId]['horarios'])) {
                                $materiasConInfo[$materiaId]['horarios'][] = $horarioTexto;
                            }
                        }
                    @endphp
                    
                    @foreach ($materiasConInfo as $materiaId => $info)
                        @php
                            $colorClase = $materiaColores[$materiaId] ?? 'bg-gray-100 border-gray-400 text-gray-900';
                        @endphp
                        <div class="p-3 border rounded-lg {{ $colorClase }}">
                            <div class="flex items-start">
                                <div class="w-1 h-full mr-3 rounded {{ $colorClase }}"></div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-bold">{{ $info['materia']->sigla }}</span>
                                        <span class="text-xs">Grupo(s): {{ implode(', ', $info['grupos']) }}</span>
                                    </div>
                                    <div class="mb-2 text-xs font-medium">{{ $info['materia']->nombre }}</div>
                                    <div class="text-xs opacity-75">
                                        <span class="font-semibold">Horarios:</span>
                                        <div class="mt-1 space-y-1">
                                            @foreach ($info['horarios'] as $horarioTexto)
                                                <div>• {{ $horarioTexto }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@else
    <p class="text-red-500">No se encontró un semestre activo.</p>
@endif
