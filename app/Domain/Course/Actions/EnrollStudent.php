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
            return Enrollment::create([
                'user_id' => $student->id,
                'course_section_id' => $section->id,
                'status' => 'active',
                'enrolled_at' => now(),
            ]);
        });
    }
}
