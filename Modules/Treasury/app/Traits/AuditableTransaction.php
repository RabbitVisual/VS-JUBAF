<?php

namespace Modules\Treasury\App\Traits;

use Illuminate\Support\Facades\Request;
use Modules\Treasury\App\Models\AuditFinancialLog;

trait AuditableTransaction
{
    /**
     * Append-only audit log. Call from service after create/update/delete within the same transaction.
     */
    public static function logAudit(
        string $action,
        string $auditableType,
        ?int $auditableId,
        ?array $oldValues,
        ?array $newValues,
        ?int $userId = null,
        ?string $ip = null
    ): void {
        AuditFinancialLog::create([
            'user_id' => $userId,
            'action' => $action,
            'auditable_type' => $auditableType,
            'auditable_id' => $auditableId ?? 0,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip' => $ip ?? Request::ip(),
        ]);
    }
}
