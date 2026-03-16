<?php

declare(strict_types=1);

namespace App\Domain\Course\Actions;

use App\Domain\Audit\Actions\LogAction;
use App\Domain\Course\Models\Course;
use App\Models\User;

class UpdateCourse
{
    public function __construct(private readonly LogAction $logAction) {}

    public function execute(User $actor, Course $course, array $data): Course
    {
        $course->update($data);

        $this->logAction->execute($actor->id, 'course.updated', Course::class, $course->id, ['code' => $course->code]);

        return $course->fresh();
    }
}
