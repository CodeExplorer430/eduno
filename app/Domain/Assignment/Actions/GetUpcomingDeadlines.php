<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Actions;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GetUpcomingDeadlines
{
    /**
     * Return assignments due within the next $days days for the given student,
     * ordered by due_at ascending.
     *
     * @return Collection<int, \stdClass>
     */
    public function execute(User $user, int $days = 7): Collection
    {
        return DB::table('assignments')
            ->join('course_sections', 'assignments.course_section_id', '=', 'course_sections.id')
            ->join('courses', 'course_sections.course_id', '=', 'courses.id')
            ->join('enrollments', function ($join) use ($user) {
                $join->on('enrollments.course_section_id', '=', 'course_sections.id')
                    ->where('enrollments.user_id', '=', $user->id)
                    ->where('enrollments.status', '=', 'active');
            })
            ->whereNotNull('assignments.published_at')
            ->whereNotNull('assignments.due_at')
            ->where('assignments.due_at', '>=', now())
            ->where('assignments.due_at', '<=', now()->addDays($days))
            ->orderBy('assignments.due_at')
            ->select([
                'assignments.id',
                'assignments.title',
                'assignments.due_at',
                'courses.code as course_code',
                'courses.title as course_name',
            ])
            ->get();
    }
}
