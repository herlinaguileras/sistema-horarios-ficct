<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'activa',
    ];

    /**
     * Una carrera tiene muchas materias (relación many-to-many).
     */
    public function materias()
    {
        return $this->belongsToMany(Materia::class, 'carrera_materia');
    }
}
