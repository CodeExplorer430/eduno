<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Notification\Actions\GetUserNotifications;
use App\Domain\Notification\Actions\MarkAllNotificationsRead;
use App\Domain\Notification\Actions\MarkNotificationRead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(GetUserNotifications $action): Response
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return Inertia::render('Notifications/Index', [
            'notifications' => $action->execute(),
            'unread_count'  => $user->unreadNotifications()->count(),
        ]);
    }

    public function show(string $notification, MarkNotificationRead $action): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        /** @var DatabaseNotification $notif */
        $notif = $user->notifications()->findOrFail($notification);

        $action->execute($notif->id);

        /** @var string $url */
        $url = $notif->data['url'] ?? route('notifications.index');

        return redirect()->away($url);
    }

    public function markAsRead(string $notification, MarkNotificationRead $action): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        /** @var DatabaseNotification $notif */
        $notif = $user->notifications()->findOrFail($notification);

        $action->execute($notif->id);

        return redirect()->back();
    }

    public function markAllAsRead(MarkAllNotificationsRead $action): RedirectResponse
    {
        $action->execute();

        return redirect()->back();
    }
}
