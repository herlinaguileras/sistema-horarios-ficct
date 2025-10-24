<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'piso',
        'capacidad',
        'tipo',
    ];

    /**
     * Un aula puede tener muchos Horarios (clases) asignados.
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }
}
