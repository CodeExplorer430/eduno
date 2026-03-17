<?php

declare(strict_types=1);

namespace App\Domain\Course\Actions;

use App\Domain\Audit\Actions\LogAction;
use App\Domain\Course\Models\Course;
use App\Support\Enums\CourseStatus;

class UpdateCourseStatus
{
    public function __construct(private readonly LogAction $logAction) {}

    public function execute(Course $course, CourseStatus $newStatus, int $actorId): void
    {
        $oldStatus = (string) ($course->getRawOriginal('status') ?? '');

        $course->update(['status' => $newStatus]);

        $this->logAction->execute(
            $actorId,
            'course.status_changed',
            Course::class,
            $course->id,
            ['old_status' => $oldStatus, 'new_status' => $newStatus->value]
        );
    }
}
