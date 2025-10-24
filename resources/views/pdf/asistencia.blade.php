<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Asistencia - {{ $semestreActivo->nombre }}</title>
    {{-- Basic styling for the PDF --}}
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #dddddd; padding: 4px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        h1 { font-size: 16px; text-align: center; margin-bottom: 5px; }
        h2 { font-size: 14px; text-align: center; margin-bottom: 15px; color: #555; }
        .teacher-header { font-size: 12px; font-weight: bold; background-color: #eee; padding: 6px; margin-top: 15px; border-top: 1px solid #ccc; }
        .group-header { font-size: 11px; font-weight: bold; color: #336699; margin-top: 8px; margin-bottom: 3px; padding-left: 5px; }
        .no-records { text-align: center; color: #777; margin-top: 20px; }
        .justification { font-size: 9px; color: #555; }
    </style>
</head>
<body>

    <h1>Reporte de Asistencia</h1>
    <h2>{{ $semestreActivo->nombre }}</h2>

    @forelse ($asistenciasAgrupadas as $docenteId => $gruposDelDocente)
        @php $primerRegistroDocente = $gruposDelDocente->first()->first(); @endphp
        <div class="teacher-header">
            Docente: {{ $primerRegistroDocente->docente->user->name ?? 'Docente Desconocido' }}
        </div>

        @foreach ($gruposDelDocente as $grupoId => $asistenciasDelGrupo)
            @php $primerRegistroGrupo = $asistenciasDelGrupo->first(); @endphp
            <div class="group-header">
                Materia: {{ $primerRegistroGrupo->horario->grupo->materia->sigla ?? 'N/A' }}
                - Grupo {{ $primerRegistroGrupo->horario->grupo->nombre ?? 'N/A' }}
                ({{ $primerRegistroGrupo->horario->grupo->materia->nombre ?? 'N/A' }})
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora Reg.</th>
                        <th>Estado</th>
                        <th>Método</th>
                        <th>Justificación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($asistenciasDelGrupo as $asistencia)
                        <tr>
                            <td>{{ $asistencia->fecha }}</td>
                            <td>{{ date('H:i:s', strtotime($asistencia->hora_registro)) }}</td>
                            <td>{{ $asistencia->estado }}</td>
                            <td>{{ $asistencia->metodo_registro ?? '-' }}</td>
                            <td class="justification">{{ $asistencia->justificacion ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @empty
        <p class="no-records">No hay registros de asistencia para este semestre.</p>
    @endforelse

</body>
</html>
