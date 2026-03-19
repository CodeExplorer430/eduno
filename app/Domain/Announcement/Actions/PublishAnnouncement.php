<?php

declare(strict_types=1);

namespace App\Domain\Announcement\Actions;

use App\Domain\Announcement\Models\Announcement;
use App\Notifications\AnnouncementPublishedNotification;
use Illuminate\Support\Facades\Notification;

class PublishAnnouncement
{
    public function handle(Announcement $announcement): Announcement
    {
        $wasUnpublished = $announcement->published_at === null;

        $announcement->update([
            'published_at' => $wasUnpublished ? now() : null,
        ]);

        if ($wasUnpublished) {
            $announcement->refresh();
            $students = $announcement->section->enrollments()
                ->with('student')
                ->get()
                ->pluck('student');

            Notification::send($students, new AnnouncementPublishedNotification($announcement));
        }

        return $announcement;
    }
}
