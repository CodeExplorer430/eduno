<?php

declare(strict_types=1);

namespace App\Domain\Course\Actions;

use App\Domain\Course\Models\Course;
use App\Enums\CourseStatus;
use Illuminate\Support\Facades\Log;

class UpdateCourse
{
    public function handle(Course $course, array $data): Course
    {
        $previousStatus = $course->status;

        $course->code = $data['code'];
        $course->title = $data['title'];
        $course->description = $data['description'] ?? null;
        $course->department = $data['department'];
        $course->term = $data['term'];
        $course->academic_year = $data['academic_year'];

        if (isset($data['status'])) {
            $newStatus = CourseStatus::from($data['status']);
            if ($previousStatus !== $newStatus) {
                Log::info('Course status changed', [
                    'course_id' => $course->id,
                    'from' => $previousStatus->value,
                    'to' => $newStatus->value,
                ]);
            }
            $course->status = $newStatus;
        }

        $course->save();

        return $course;
    }
}
