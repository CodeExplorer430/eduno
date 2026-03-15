<?php

declare(strict_types=1);

namespace App\Domain\Course\Actions;

use App\Domain\Course\Models\Course;
use App\Enums\CourseStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateCourse
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(User $creator, array $data): Course
    {
        if (Course::where('code', $data['code'])->exists()) {
            throw ValidationException::withMessages([
                'code' => 'A course with this code already exists.',
            ]);
        }

        $course = Course::create([
            'code' => $data['code'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'department' => $data['department'],
            'term' => $data['term'],
            'academic_year' => $data['academic_year'],
            'status' => CourseStatus::Draft,
            'created_by' => $creator->id,
        ]);

        DB::table('audit_logs')->insert([
            'actor_id' => $creator->id,
            'action' => 'course.created',
            'entity_type' => Course::class,
            'entity_id' => $course->id,
            'metadata' => json_encode(['code' => $course->code, 'title' => $course->title]),
            'created_at' => now(),
        ]);

        return $course;
    }
}
