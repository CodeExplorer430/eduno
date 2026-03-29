<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Domain\Audit\Actions\LogAction;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    public function __construct(private readonly LogAction $logAction)
    {
    }

    public function handle(Failed $event): void
    {
        $userId = $event->user?->getAuthIdentifier();

        $this->logAction->execute(
            actorId: $userId,
            action: 'auth.login_failed',
            entityType: 'user',
            entityId: $userId,
            metadata: [
                'guard'       => $event->guard,
                'credentials' => isset($event->credentials['email'])
                    ? ['email' => $event->credentials['email']]
                    : [],
            ],
        );
    }
}
