<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait HasAuditLog
{
    protected static function bootHasAuditLog()
    {
        static::created(function (Model $model) {
            $model->logAuditAction('created');
        });

        static::updated(function (Model $model) {
            $model->logAuditAction('updated');
        });

        static::deleted(function (Model $model) {
            $model->logAuditAction('deleted');
        });
    }

    public function logAuditAction(string $action, array $extraData = [])
    {
        $oldValues = $action === 'updated' ? $this->getOriginal() : null;
        $newValues = $action !== 'deleted' ? $this->getAttributes() : null;

        $logData = [
            'user_id' => Auth::id(),
            'entity_type' => get_class($this),
            'entity_id' => $this->id,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        if (!empty($extraData)) {
            $logData['metadata'] = $extraData;
        }

        AuditLog::create($logData);
    }

    public function logAccessAction()
    {
        $this->logAuditAction('accessed', [
            'route' => request()->route()->getName(),
            'method' => request()->method(),
        ]);
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'entity');
    }
}