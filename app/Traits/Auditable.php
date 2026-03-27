<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\AuditAction;
use App\Models\AuditLog;

/**
 * Trait para registrar automaticamente operaciones CUD en audit_logs.
 * Aplicar en cualquier modelo que requiera auditoria.
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->registerAudit(AuditAction::CREATED, [], $model->getAttributes());
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            if (empty($dirty)) {
                return;
            }

            $oldValues = collect($dirty)
                ->keys()
                ->mapWithKeys(fn (string $key) => [$key => $model->getOriginal($key)])
                ->toArray();

            $model->registerAudit(AuditAction::UPDATED, $oldValues, $dirty);
        });

        static::deleted(function ($model) {
            $model->registerAudit(AuditAction::DELETED, $model->getAttributes(), []);
        });

        if (method_exists(static::class, 'bootSoftDeletes')) {
            static::restored(function ($model) {
                $model->registerAudit(AuditAction::RESTORED, [], $model->getAttributes());
            });
        }
    }

    protected function registerAudit(AuditAction $action, array $oldValues, array $newValues): void
    {
        $hidden = $this->getHidden();
        $oldValues = array_diff_key($oldValues, array_flip($hidden));
        $newValues = array_diff_key($newValues, array_flip($hidden));

        AuditLog::create([
            'user_id' => auth()->id(),
            'auditable_type' => $this->getMorphClass(),
            'auditable_id' => $this->getKey(),
            'action' => $action,
            'old_values' => empty($oldValues) ? null : $oldValues,
            'new_values' => empty($newValues) ? null : $newValues,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'created_at' => now(),
        ]);
    }
}
