<?php

declare(strict_types=1);

namespace App\Domain\Course\Actions;

use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnrollStudent
{
    public function handle(User $student, CourseSection $section): Enrollment
    {
        $exists = Enrollment::where('user_id', $student->id)
            ->where('course_section_id', $section->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'enrollment' => 'Student is already enrolled in this section.',
            ]);
        }

        return DB::transaction(function () use ($student, $section): Enrollment {
            $enrollment = Enrollment::create([
                'user_id' => $student->id,
                'course_section_id' => $section->id,
                'status' => 'active',
                'enrolled_at' => now(),
            ]);

            DB::table('audit_logs')->insert([
                'actor_id' => $student->id,
                'action' => 'enrollment.created',
                'entity_type' => Enrollment::class,
                'entity_id' => $enrollment->id,
                'metadata' => json_encode([
                    'student_id' => $student->id,
                    'course_section_id' => $section->id,
                ]),
                'created_at' => now(),
            ]);

            return $enrollment;
        });
    }
}
