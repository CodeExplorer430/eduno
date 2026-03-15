<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Domain\Assignment\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeadlineReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Assignment $assignment)
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
        return (new MailMessage())
            ->subject("Reminder: {$this->assignment->title} due soon")
            ->greeting('Hello!')
            ->line("Your assignment \"{$this->assignment->title}\" is due soon.")
            ->line('Due: '.($this->assignment->due_at->toDayDateTimeString()))
            ->action('View Assignment', route('assignments.show', $this->assignment->id));
    }
}
