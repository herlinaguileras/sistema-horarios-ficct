<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'semestre_id',
        'materia_id',
        'docente_id',
        'nombre',
    ];

    /**
     * Un grupo pertenece a un Semestre.
     */
    public function semestre()
    {
        return $this->belongsTo(Semestre::class);
    }

    /**
     * Un grupo pertenece a una Materia.
     */
    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    /**
     * Un grupo es asignado a un Docente.
     */
    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }

    /**
     * Un grupo tiene muchos Horarios (clases).
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }
}
