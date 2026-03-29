<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Domain\Audit\Actions\LogAction;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function __construct(private readonly LogAction $logAction)
    {
    }

    public function handle(Login $event): void
    {
        $this->logAction->execute(
            actorId: $event->user->getAuthIdentifier(),
            action: 'auth.login',
            entityType: 'user',
            entityId: $event->user->getAuthIdentifier(),
            metadata: ['guard' => $event->guard],
        );
    }
}
