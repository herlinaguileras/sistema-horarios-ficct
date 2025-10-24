<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    // Indicamos que NO usamos la columna 'updated_at'
    const UPDATED_AT = null;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'details',
        'ip_address',
        'user_agent',
        // 'created_at' se maneja automáticamente
    ];

    /**
     * Un registro de auditoría pertenece a un Usuario (el que hizo la acción).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
