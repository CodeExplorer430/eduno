<?php

declare(strict_types=1);

namespace App\Domain\Course\Actions;

use App\Domain\Course\Models\Course;
use App\Enums\CourseStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateCourse
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(Course $course, array $data, User $actor): Course
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

        DB::table('audit_logs')->insert([
            'actor_id' => $actor->id,
            'action' => 'course.updated',
            'entity_type' => Course::class,
            'entity_id' => $course->id,
            'metadata' => json_encode(['code' => $course->code, 'title' => $course->title]),
            'created_at' => now(),
        ]);

        return $course;
    }
}
