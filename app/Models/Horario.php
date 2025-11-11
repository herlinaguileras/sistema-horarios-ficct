<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Asistencia;

class Horario extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'grupo_id',
        'aula_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];

    /**
     * Obtener el nombre del día de la semana
     */
    public function getDiaNombreAttribute()
    {
        $dias = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo'
        ];

        return $dias[$this->dia_semana] ?? 'Desconocido';
    }

    /**
     * Un horario pertenece a un Grupo.
     */
    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    /**
     * Un horario se asigna a un Aula.
     */
    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }

    /**
     * Un horario puede tener muchos registros de asistencia.
     */
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }
}
