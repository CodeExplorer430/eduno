<?php

declare(strict_types=1);

namespace App\Domain\Announcement\Actions;

use App\Domain\Announcement\Models\Announcement;
use App\Domain\Course\Models\CourseSection;
use App\Jobs\SendAnnouncementNotification;
use App\Models\User;

class CreateAnnouncement
{
    public function execute(User $author, CourseSection $section, array $data): Announcement
    {
        $announcement = Announcement::create([
            'course_section_id' => $section->id,
            'title' => $data['title'],
            'body' => $data['body'],
            'published_at' => $data['published_at'] ?? now(),
            'created_by' => $author->id,
        ]);

        SendAnnouncementNotification::dispatch($announcement);

        return $announcement;
    }
}
