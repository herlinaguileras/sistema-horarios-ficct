<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
protected $fillable = [
    'horario_id',
    'docente_id',
    'fecha',
    'hora_registro',
    'estado',
    'metodo_registro',
    'justificacion', // <--- Make absolutely sure this line is present
];


    /**
     * Un registro de asistencia pertenece a un Horario específico.
     */
    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }

    /**
     * Un registro de asistencia pertenece a un Docente.
     */
    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }
}
