<?php

declare(strict_types=1);

namespace App\Domain\Audit\Actions;

use App\Domain\Audit\Models\AuditLog;
use Illuminate\Support\Facades\Log;

class LogAction
{
    public function execute(
        ?int $actorId,
        string $action,
        string $entityType,
        ?int $entityId,
        ?array $metadata = null
    ): void {
        try {
            AuditLog::create([
                'actor_id' => $actorId,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'metadata' => $metadata,
            ]);
        } catch (\Throwable $e) {
            Log::error('Audit log failed: '.$e->getMessage(), [
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
            ]);
        }
    }
}
