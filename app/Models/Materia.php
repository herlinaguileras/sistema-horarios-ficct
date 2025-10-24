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
    'sigla', // <-- CAMBIO AQUÍ
    'nivel_semestre',
    'carrera',
];

    /**
     * Una materia puede estar en muchos Grupos.
     */
    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }
}
