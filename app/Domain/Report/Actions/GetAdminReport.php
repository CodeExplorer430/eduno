<?php

declare(strict_types=1);

namespace App\Domain\Report\Actions;

use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Submission\Models\Submission;
use App\Enums\UserRole;
use App\Models\User;

class GetAdminReport
{
    /**
     * @return array<string, int>
     */
    public function handle(): array
    {
        return [
            'total_courses' => Course::count(),
            'total_sections' => CourseSection::count(),
            'total_students' => User::where('role', UserRole::Student)->count(),
            'total_submissions' => Submission::count(),
            'late_submissions' => Submission::where('is_late', true)->count(),
            'graded_submissions' => Submission::whereHas('grade')->count(),
        ];
    }
}
