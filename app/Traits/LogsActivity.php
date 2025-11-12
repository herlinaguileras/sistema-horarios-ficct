<?php

namespace App\Traits;

use App\Models\AuditLog;

trait LogsActivity
{
    /**
     * Registrar una acción en la bitácora
     *
     * @param string $action Descripción de la acción (ej: 'CREATE_MATERIA', 'UPDATE_DOCENTE')
     * @param string|null $modelType Tipo de modelo afectado (ej: 'App\Models\Materia')
     * @param int|null $modelId ID del modelo afectado
     * @param array|null $details Detalles adicionales en formato array
     * @return AuditLog
     */
    protected function logActivity(
        string $action,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $details = null
    ): AuditLog {
        return AuditLog::logAction($action, $modelType, $modelId, $details);
    }

    /**
     * Log de creación
     */
    protected function logCreate($model, array $additionalDetails = []): AuditLog
    {
        $modelName = class_basename($model);

        return $this->logActivity(
            "CREATE_{$modelName}",
            get_class($model),
            $model->id,
            array_merge([
                'model' => $modelName,
                'model_id' => $model->id,
                'action_type' => 'create',
            ], $additionalDetails)
        );
    }

    /**
     * Log de actualización
     */
    protected function logUpdate($model, array $changes, array $additionalDetails = []): AuditLog
    {
        $modelName = class_basename($model);

        return $this->logActivity(
            "UPDATE_{$modelName}",
            get_class($model),
            $model->id,
            array_merge([
                'model' => $modelName,
                'model_id' => $model->id,
                'action_type' => 'update',
                'changes' => $changes,
            ], $additionalDetails)
        );
    }

    /**
     * Log de eliminación
     */
    protected function logDelete($model, array $additionalDetails = []): AuditLog
    {
        $modelName = class_basename($model);

        return $this->logActivity(
            "DELETE_{$modelName}",
            get_class($model),
            $model->id,
            array_merge([
                'model' => $modelName,
                'model_id' => $model->id,
                'action_type' => 'delete',
                'deleted_data' => $model->toArray(),
            ], $additionalDetails)
        );
    }

    /**
     * Log de login
     */
    protected function logLogin(?string $email = null): AuditLog
    {
        return $this->logActivity(
            'LOGIN',
            'App\Models\User',
            auth()->id(),
            [
                'action_type' => 'authentication',
                'email' => $email ?? auth()->user()->email ?? 'unknown',
            ]
        );
    }

    /**
     * Log de logout
     */
    protected function logLogout(): AuditLog
    {
        return $this->logActivity(
            'LOGOUT',
            'App\Models\User',
            auth()->id(),
            [
                'action_type' => 'authentication',
            ]
        );
    }

    /**
     * Log de importación
     */
    protected function logImport(string $type, int $recordsCount, array $additionalDetails = []): AuditLog
    {
        return $this->logActivity(
            "IMPORT_{$type}",
            null,
            null,
            array_merge([
                'action_type' => 'import',
                'import_type' => $type,
                'records_imported' => $recordsCount,
            ], $additionalDetails)
        );
    }

    /**
     * Log de exportación
     */
    protected function logExport(string $type, int $recordsCount, array $additionalDetails = []): AuditLog
    {
        return $this->logActivity(
            "EXPORT_{$type}",
            null,
            null,
            array_merge([
                'action_type' => 'export',
                'export_type' => $type,
                'records_exported' => $recordsCount,
            ], $additionalDetails)
        );
    }

    /**
     * Log de acción personalizada
     */
    protected function logCustomAction(string $actionName, array $details = []): AuditLog
    {
        return $this->logActivity(
            strtoupper($actionName),
            null,
            null,
            array_merge([
                'action_type' => 'custom',
            ], $details)
        );
    }
}
