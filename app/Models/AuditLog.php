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
        'endpoint',
        'http_method',
        'model_type',
        'model_id',
        'details',
        'ip_address',
        'user_agent',
    ];

    /**
     * Casts de atributos
     */
    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Un registro de auditoría pertenece a un Usuario (el que hizo la acción).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Método estático para crear un log de auditoría de manera simplificada
     *
     * @param string $action
     * @param string|null $modelType
     * @param int|null $modelId
     * @param array|null $details
     * @return AuditLog
     */
    public static function logAction(
        string $action,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $details = null
    ): AuditLog {
        $request = request();

        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'endpoint' => $request->path(),
            'http_method' => $request->method(),
            'model_type' => $modelType,
            'model_id' => $modelId,
            'details' => $details,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Scope: Filtrar por usuario
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Filtrar por acción
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', 'like', "%{$action}%");
    }

    /**
     * Scope: Filtrar por modelo
     */
    public function scopeByModel($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope: Filtrar por rango de fechas
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Filtrar por IP
     */
    public function scopeByIp($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Obtener el nombre del usuario de forma segura
     */
    public function getUserNameAttribute(): string
    {
        return $this->user ? $this->user->name : 'Usuario Eliminado';
    }

    /**
     * Formatear la acción para mostrar
     */
    public function getFormattedActionAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->action));
    }
}
