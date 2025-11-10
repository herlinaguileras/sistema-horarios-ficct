<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'sigla',
        'nivel_semestre',
    ];

    /**
     * Una materia puede estar en muchos Grupos.
     */
    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }

    /**
     * Una materia pertenece a muchas carreras (relación many-to-many).
     */
    public function carreras()
    {
        return $this->belongsToMany(Carrera::class, 'carrera_materia');
    }
}
