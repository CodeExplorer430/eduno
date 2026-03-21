<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domain\Announcement\Models\Announcement;
use App\Domain\Course\Models\CourseSection;
use App\Mail\AnnouncementPublishedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendAnnouncementNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Announcement $announcement)
    {
    }

    public function handle(): void
    {
        /** @var CourseSection $courseSection */
        $courseSection = $this->announcement->courseSection;
        $students = $courseSection->enrollments()->with('user')->get()->pluck('user');

        foreach ($students as $student) {
            Mail::to($student->email)->queue(new AnnouncementPublishedMail($this->announcement));
        }
    }
}
