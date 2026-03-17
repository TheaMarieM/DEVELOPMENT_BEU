<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    /**
     * Boot the trait
     */
    protected static function bootLogsActivity(): void
    {
        // Log when model is created
        static::created(function ($model) {
            if (auth()->check()) {
                ActivityLog::logActivity(
                    action: 'created',
                    modelType: get_class($model),
                    modelId: $model->id,
                    description: static::getCreatedDescription($model),
                    newValues: $model->getAttributes()
                );
            }
        });

        // Log when model is updated
        static::updated(function ($model) {
            if (auth()->check()) {
                $changes = $model->getChanges();
                $original = array_intersect_key($model->getOriginal(), $changes);
                
                // Don't log if only timestamps changed
                $ignoredFields = ['updated_at', 'created_at'];
                $significantChanges = array_diff_key($changes, array_flip($ignoredFields));
                
                if (!empty($significantChanges)) {
                    ActivityLog::logActivity(
                        action: 'updated',
                        modelType: get_class($model),
                        modelId: $model->id,
                        description: static::getUpdatedDescription($model, $changes),
                        oldValues: $original,
                        newValues: $changes
                    );
                }
            }
        });

        // Log when model is deleted
        static::deleted(function ($model) {
            if (auth()->check()) {
                ActivityLog::logActivity(
                    action: 'deleted',
                    modelType: get_class($model),
                    modelId: $model->id,
                    description: static::getDeletedDescription($model),
                    oldValues: $model->getAttributes()
                );
            }
        });
    }

    /**
     * Get description for created action
     */
    protected static function getCreatedDescription($model): string
    {
        $modelName = class_basename($model);
        return "{$modelName} was created";
    }

    /**
     * Get description for updated action
     */
    protected static function getUpdatedDescription($model, array $changes): string
    {
        $modelName = class_basename($model);
        $changedFields = implode(', ', array_keys($changes));
        return "{$modelName} was updated. Changed: {$changedFields}";
    }

    /**
     * Get description for deleted action
     */
    protected static function getDeletedDescription($model): string
    {
        $modelName = class_basename($model);
        return "{$modelName} was deleted";
    }

    /**
     * Log a custom activity for this model
     */
    public function logCustomActivity(string $action, ?string $description = null, ?array $metadata = null): ActivityLog
    {
        return ActivityLog::logActivity(
            action: $action,
            modelType: get_class($this),
            modelId: $this->id,
            description: $description ?? "{$action} action on " . class_basename($this),
            newValues: $metadata
        );
    }

    /**
     * Get all activity logs for this model
     */
    public function activityLogs()
    {
        return ActivityLog::where('model_type', get_class($this))
            ->where('model_id', $this->id)
            ->orderByDesc('created_at');
    }
}
