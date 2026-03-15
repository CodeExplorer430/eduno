<?php

declare(strict_types=1);

namespace App\Domain\Announcement\Actions;

use App\Domain\Announcement\Models\Announcement;
use App\Domain\Course\Models\CourseSection;
use App\Models\User;

class CreateAnnouncement
{
    public function handle(CourseSection $section, array $data, User $author): Announcement
    {
        return Announcement::create([
            'course_section_id' => $section->id,
            'title' => $data['title'],
            'body' => $data['body'],
            'created_by' => $author->id,
        ]);
    }
}
