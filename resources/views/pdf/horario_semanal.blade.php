<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horario Semanal - {{ $semestreActivo->nombre }}</title>
    {{-- Basic styling for the PDF table --}}
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #dddddd; padding: 6px; text-align: left; font-size: 10px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        h1 { font-size: 16px; text-align: center; margin-bottom: 5px; }
        h2 { font-size: 14px; text-align: center; margin-bottom: 15px; color: #555; }
        .day-header { font-size: 12px; font-weight: bold; background-color: #eef; padding: 8px; margin-top: 10px; border-top: 2px solid #ccc; }
    </style>
</head>
<body>

    <h1>Horario Semanal</h1>
    <h2>{{ $semestreActivo->nombre }}</h2>

    {{-- Loop through days --}}
    @forelse ($diasSemana as $numDia => $nombreDia)
        @if ($horariosPorDia->has($numDia))
            <div class="day-header">{{ $nombreDia }}</div>
            <table>
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Materia</th>
                        <th>Grupo</th>
                        <th>Docente</th>
                        <th>Aula</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Loop through schedules for the day --}}
                    @foreach ($horariosPorDia[$numDia] as $horario)
                        <tr>
                            <td>{{ date('H:i', strtotime($horario->hora_inicio)) }} - {{ date('H:i', strtotime($horario->hora_fin)) }}</td>
                            <td>{{ $horario->grupo->materia->sigla }} - {{ $horario->grupo->materia->nombre }}</td>
                            <td>{{ $horario->grupo->nombre }}</td>
                            <td>{{ $horario->grupo->docente->user->name }}</td>
                            <td>{{ $horario->aula->nombre }} (P.{{ $horario->aula->piso }})</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @empty
        {{-- This case shouldn't happen if $diasSemana is populated, but good practice --}}
    @endforelse

    @if ($horariosPorDia->isEmpty())
        <p style="text-align: center; color: #777;">No hay horarios programados para este semestre.</p>
    @endif

</body>
</html>
