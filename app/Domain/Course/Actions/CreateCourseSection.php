<?php

declare(strict_types=1);

namespace App\Domain\Course\Actions;

use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Models\User;

class CreateCourseSection
{
    public function handle(Course $course, User $instructor, array $data): CourseSection
    {
        return CourseSection::create([
            'course_id' => $course->id,
            'section_name' => $data['section_name'],
            'instructor_id' => $instructor->id,
            'schedule_text' => $data['schedule_text'] ?? null,
        ]);
    }
}
