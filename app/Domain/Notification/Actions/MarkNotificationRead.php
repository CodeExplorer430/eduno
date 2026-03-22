<?php

declare(strict_types=1);

namespace App\Domain\Notification\Actions;

use Illuminate\Notifications\DatabaseNotification;

class MarkNotificationRead
{
    public function execute(string $notificationId): void
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $notification = $user->notifications()->findOrFail($notificationId);

        /** @var DatabaseNotification $notification */
        $notification->markAsRead();
    }
}
