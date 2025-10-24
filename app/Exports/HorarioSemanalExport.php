<?php

namespace App\Exports;

use App\Models\Horario;
use App\Models\Semestre; // Import Semestre
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // For column width

class HorarioSemanalExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $semestreId;
    protected $diasSemana;

    // Receive the active semester ID when the export is created
    public function __construct(int $semestreId)
    {
        $this->semestreId = $semestreId;
        $this->diasSemana = [ // Array to convert number to name
            1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
        ];
    }

    /**
     * Define the database query to fetch the data.
     */
    public function query()
    {
        // Same query as in DashboardController, ordered for the report
        return Horario::query()
            ->whereHas('grupo', function ($query) {
                $query->where('semestre_id', $this->semestreId);
            })
            ->with(['grupo.materia', 'grupo.docente.user', 'aula'])
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio');
    }

    /**
     * Define the header row for the Excel file.
     */
    public function headings(): array
    {
        return [
            'Día',
            'Hora Inicio',
            'Hora Fin',
            'Materia (Sigla)',
            'Materia (Nombre)',
            'Grupo',
            'Docente',
            'Aula',
            'Piso',
        ];
    }

    /**
     * Map the data from each $horario object to an array for the row.
     *
     * @param Horario $horario
     */
    public function map($horario): array
    {
        return [
            // Convert day number back to name
            $this->diasSemana[$horario->dia_semana] ?? 'Día Inválido',
            // Format time without seconds
            date('H:i', strtotime($horario->hora_inicio)),
            date('H:i', strtotime($horario->hora_fin)),
            $horario->grupo->materia->sigla ?? 'N/A',
            $horario->grupo->materia->nombre ?? 'N/A',
            $horario->grupo->nombre ?? 'N/A',
            $horario->grupo->docente->user->name ?? 'N/A',
            $horario->aula->nombre ?? 'N/A',
            $horario->aula->piso ?? 'N/A',
        ];
    }
}
