<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Domain\Announcement\Models\Announcement;
use App\Domain\Course\Models\CourseSection;
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
        return ['database', 'mail'];
    }

    /**
     * @return array<string, string>
     */
    public function toDatabase(object $notifiable): array
    {
        /** @var CourseSection|null $section */
        $section = $this->announcement->courseSection;
        $courseModel = $section?->course;
        $course = $courseModel !== null ? $courseModel->code : 'Course';
        $title = $this->announcement->title;

        return [
            'message' => "New announcement in {$course}: \"{$title}\"",
            'url'     => route('student.announcements.index'),
            'type'    => 'announcement_published',
        ];
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
