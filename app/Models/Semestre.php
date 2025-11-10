<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semestre extends Model
{
    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    /**
     * Los atributos que deben ser casteados.
     */
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    /**
     * Estados posibles del semestre.
     */
    const ESTADO_PLANIFICACION = 'Planificación';
    const ESTADO_ACTIVO = 'Activo';
    const ESTADO_TERMINADO = 'Terminado';

    /**
     * Obtiene todos los estados disponibles.
     */
    public static function getEstados()
    {
        return [
            self::ESTADO_PLANIFICACION,
            self::ESTADO_ACTIVO,
            self::ESTADO_TERMINADO,
        ];
    }

    /**
     * Verifica si el semestre está activo.
     */
    public function isActivo()
    {
        return $this->estado === self::ESTADO_ACTIVO;
    }

    /**
     * Scope para obtener solo el semestre activo.
     */
    public function scopeActivo($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVO);
    }

    /**
     * Un semestre tiene muchos Grupos.
     */
    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }
}
