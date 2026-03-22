<?php

declare(strict_types=1);

namespace App\Domain\Notification\Actions;

class MarkAllNotificationsRead
{
    public function execute(): void
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $user->unreadNotifications->markAsRead();
    }
}
