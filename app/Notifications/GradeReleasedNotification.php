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
        return ['database', 'mail'];
    }

    /**
     * @return array<string, string>
     */
    public function toDatabase(object $notifiable): array
    {
        $assignment = $this->grade->submission->assignment;
        $title = $assignment->title;
        $score = (string) $this->grade->score;
        $max = (string) $assignment->max_score;

        return [
            'message' => "Your grade for \"{$title}\" has been released: {$score}/{$max}",
            'url'     => route('student.submissions.show', $this->grade->submission_id),
            'type'    => 'grade_released',
        ];
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
