<?php

namespace Modules\Diretoria\App\Services;

use Illuminate\Database\Eloquent\Model;
use Modules\Diretoria\App\Models\DiretoriaAuditLog;

class DiretoriaAuditService
{
    /**
     * Register an audit entry for a given entity and action.
     */
    public function log(string $action, Model $entity, array $payload = []): void
    {
        if (! class_exists(DiretoriaAuditLog::class)) {
            return;
        }

        try {
            DiretoriaAuditLog::create([
                'user_id' => auth()->id(),
                'entity_type' => get_class($entity),
                'entity_id' => $entity->getKey(),
                'action' => $action,
                'payload' => $payload,
            ]);
        } catch (\Throwable $e) {
            \Log::warning('diretoria audit log failed: '.$e->getMessage(), [
                'action' => $action,
                'entity_type' => get_class($entity),
                'entity_id' => $entity->getKey(),
            ]);
        }
    }
}
