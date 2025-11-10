<?php

namespace App\Exports;

use App\Models\Asistencia;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AsistenciaExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $semestreId;
    protected $filtros;

    public function __construct(int $semestreId, array $filtros = [])
    {
        $this->semestreId = $semestreId;
        $this->filtros = $filtros;
    }

    /**
     * Define the query to fetch attendance data for the active semester.
     */
    public function query()
    {
        // Same query logic as in DashboardController
        $query = Asistencia::query()
            ->whereHas('horario.grupo', function ($query) {
                $query->where('semestre_id', $this->semestreId);
            })
            ->with([
                'docente.user',
                'horario.grupo.materia',
                'horario.aula' // Added Aula for context if needed, though not mapped below
            ]);

        // Apply filters
        if (!empty($this->filtros['filtro_asist_docente_id'])) {
            $query->where('docente_id', $this->filtros['filtro_asist_docente_id']);
        }
        if (!empty($this->filtros['filtro_asist_materia_id'])) {
            $query->whereHas('horario.grupo', function ($q) {
                $q->where('materia_id', $this->filtros['filtro_asist_materia_id']);
            });
        }
        if (!empty($this->filtros['filtro_asist_grupo_id'])) {
            $query->whereHas('horario', function ($q) {
                $q->where('grupo_id', $this->filtros['filtro_asist_grupo_id']);
            });
        }
        if (!empty($this->filtros['filtro_asist_estado'])) {
            $query->where('estado', $this->filtros['filtro_asist_estado']);
        }
        if (!empty($this->filtros['filtro_asist_metodo'])) {
            $query->where('metodo_registro', $this->filtros['filtro_asist_metodo']);
        }
        if (!empty($this->filtros['filtro_asist_fecha_inicio'])) {
            $query->where('fecha', '>=', $this->filtros['filtro_asist_fecha_inicio']);
        }
        if (!empty($this->filtros['filtro_asist_fecha_fin'])) {
            $query->where('fecha', '<=', $this->filtros['filtro_asist_fecha_fin']);
        }

        return $query
            ->orderBy('docente_id')
            ->orderBy('horario_id') // Use horario_id for grouping consistency
            ->orderBy('fecha', 'asc') // Order chronologically within group
            ->orderBy('hora_registro', 'asc');
    }

    /**
     * Define the header row.
     */
    public function headings(): array
    {
        return [
            'Docente',
            'Materia (Sigla)',
            'Materia (Nombre)',
            'Grupo',
            'Fecha Asistencia',
            'Hora Registro',
            'Estado',
            'Método',
            'Justificación',
        ];
    }

    /**
     * Map the data for each row.
     * @param Asistencia $asistencia
     */
    public function map($asistencia): array
    {
        return [
            $asistencia->docente->user->name ?? 'N/A',
            $asistencia->horario->grupo->materia->sigla ?? 'N/A',
            $asistencia->horario->grupo->materia->nombre ?? 'N/A',
            $asistencia->horario->grupo->nombre ?? 'N/A',
            $asistencia->fecha,
            date('H:i:s', strtotime($asistencia->hora_registro)), // Format time
            $asistencia->estado,
            $asistencia->metodo_registro ?? '-',
            $asistencia->justificacion ?? '-',
        ];
    }
}
