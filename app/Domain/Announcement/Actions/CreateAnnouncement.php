<?php

declare(strict_types=1);

namespace App\Domain\Announcement\Actions;

use App\Domain\Announcement\Models\Announcement;
use App\Domain\Audit\Actions\LogAction;
use App\Domain\Course\Models\CourseSection;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateAnnouncement
{
    public function __construct(private readonly LogAction $logAction)
    {
    }

    public function handle(CourseSection $section, array $data, User $author): Announcement
    {
        $announcement = DB::transaction(function () use ($section, $data, $author): Announcement {
            $announcement = Announcement::create([
                'course_section_id' => $section->id,
                'title' => $data['title'],
                'body' => $data['body'],
                'created_by' => $author->id,
            ]);

            $this->logAction->execute(
                $author->id,
                'announcement.created',
                Announcement::class,
                $announcement->id,
                ['title' => $announcement->title, 'section_id' => $section->id],
            );

            return $announcement;
        });

        return $announcement;
    }
}
