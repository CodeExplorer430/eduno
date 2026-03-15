<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Domain\Announcement\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnnouncementPublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Announcement $announcement)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $excerpt = mb_substr($this->announcement->body, 0, 200);

        return (new MailMessage())
            ->subject("New Announcement: {$this->announcement->title}")
            ->greeting('Hello!')
            ->line($excerpt)
            ->action('View Announcement', route('announcements.show', $this->announcement->id));
    }
}
