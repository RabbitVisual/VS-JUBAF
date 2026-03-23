<?php

namespace Modules\ChurchCouncil\App\Services;

use Illuminate\Database\Eloquent\Model;
use Modules\ChurchCouncil\App\Models\CouncilAuditLog;

class CouncilAuditService
{
    /**
     * Register an audit entry for a given entity and action.
     */
    public function log(string $action, Model $entity, array $payload = []): void
    {
        if (! class_exists(CouncilAuditLog::class)) {
            return;
        }

        try {
            CouncilAuditLog::create([
                'user_id' => auth()->id(),
                'entity_type' => get_class($entity),
                'entity_id' => $entity->getKey(),
                'action' => $action,
                'payload' => $payload,
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Council audit log failed: '.$e->getMessage(), [
                'action' => $action,
                'entity_type' => get_class($entity),
                'entity_id' => $entity->getKey(),
            ]);
        }
    }
}

