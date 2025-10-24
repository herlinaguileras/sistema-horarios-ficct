<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'codigo_docente',
        'carnet_identidad',
        'telefono',
        'titulo',
        'facultad',
        'estado',
        'fecha_contratacion',
    ];


    /**
     * Obtiene el usuario (cuenta) al que pertenece este perfil de docente.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
 * Obtiene todos los títulos asociados al docente.
 */
public function titulos()
{
    return $this->hasMany(Titulo::class);
}
}
