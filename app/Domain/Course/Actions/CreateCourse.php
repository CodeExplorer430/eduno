<?php

declare(strict_types=1);

namespace App\Domain\Course\Actions;

use App\Domain\Course\Models\Course;
use App\Enums\CourseStatus;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CreateCourse
{
    public function handle(User $creator, array $data): Course
    {
        if (Course::where('code', $data['code'])->exists()) {
            throw ValidationException::withMessages([
                'code' => 'A course with this code already exists.',
            ]);
        }

        return Course::create([
            'code' => $data['code'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'department' => $data['department'],
            'term' => $data['term'],
            'academic_year' => $data['academic_year'],
            'status' => CourseStatus::Draft,
            'created_by' => $creator->id,
        ]);
    }
}
