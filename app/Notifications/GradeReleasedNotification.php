<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Domain\Submission\Models\Grade;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GradeReleasedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Grade $grade)
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
        $assignment = $this->grade->submission->assignment;

        return (new MailMessage())
            ->subject("Grade Released: {$assignment->title}")
            ->greeting('Hello!')
            ->line("Your score: {$this->grade->score} / {$assignment->max_score}")
            ->action('View Submission', route('submissions.show', $this->grade->submission_id));
    }
}
