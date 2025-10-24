<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Titulo extends Model
{
    use HasFactory;

    protected $fillable = ['docente_id', 'nombre']; // Campos que permitimos rellenar

    /**
     * Un título pertenece a un docente.
     */
    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }
}
