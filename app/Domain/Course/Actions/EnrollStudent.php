<?php

declare(strict_types=1);

namespace App\Domain\Course\Actions;

use App\Domain\Audit\Actions\LogAction;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EnrollStudent
{
    public function __construct(private readonly LogAction $logAction) {}

    public function execute(User $student, CourseSection $section): Enrollment
    {
        return DB::transaction(function () use ($student, $section): Enrollment {
            $enrollment = Enrollment::create([
                'user_id' => $student->id,
                'course_section_id' => $section->id,
                'status' => 'active',
            ]);

            $this->logAction->execute(
                $student->id,
                'enrollment.created',
                CourseSection::class,
                $section->id,
                ['student_id' => $student->id],
            );

            return $enrollment;
        });
    }
}
