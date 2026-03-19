<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Domain\Submission\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSubmissionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Submission $submission)
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
        $assignment = $this->submission->assignment;
        $student = $this->submission->student;

        return (new MailMessage())
            ->subject("New Submission: {$assignment->title}")
            ->greeting('Hello!')
            ->line("Student: {$student->name}")
            ->line('Submitted at: '.($this->submission->submitted_at->toFormattedDateString()))
            ->action('View Submissions', route('assignments.submissions.index', $assignment->id));
    }
}
