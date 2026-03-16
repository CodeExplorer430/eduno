<?php

declare(strict_types=1);

namespace App\Domain\Course\Actions;

use App\Domain\Audit\Actions\LogAction;
use App\Domain\Course\Models\Course;
use App\Models\User;

class CreateCourse
{
    public function __construct(private readonly LogAction $logAction) {}

    public function execute(User $creator, array $data): Course
    {
        $course = Course::create([
            'code' => $data['code'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'department' => $data['department'],
            'term' => $data['term'],
            'academic_year' => $data['academic_year'],
            'status' => $data['status'] ?? 'draft',
            'created_by' => $creator->id,
        ]);

        $this->logAction->execute($creator->id, 'course.created', Course::class, $course->id, ['code' => $course->code]);

        return $course;
    }
}
