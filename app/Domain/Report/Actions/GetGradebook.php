<?php

declare(strict_types=1);

namespace App\Domain\Report\Actions;

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Submission\Models\Submission;
use Illuminate\Support\Collection;

class GetGradebook
{
    /**
     * Build the grade matrix for a course section.
     *
     * @return array{section: CourseSection, assignments: Collection<int, Assignment>, students: Collection<int, mixed>, averages: array<int, float|null>}
     */
    public function __invoke(CourseSection $section): array
    {
        $section->load('course');

        /** @var Collection<int, Assignment> $assignments */
        $assignments = Assignment::where('course_section_id', $section->id)
            ->whereNotNull('published_at')
            ->orderBy('due_at')
            ->get(['id', 'title', 'max_score', 'due_at']);

        /** @var Collection<int, int> $assignmentIds */
        $assignmentIds = $assignments->pluck('id');

        /** @var Collection<int, Enrollment> $enrollments */
        $enrollments = $section->enrollments()
            ->where('status', 'active')
            ->with('student:id,name')
            ->get();

        // All graded submissions for these assignments, ordered so the highest
        // attempt_no comes first — used to keep only the best graded attempt per cell.
        /** @var Collection<int, Submission> $submissions */
        $submissions = Submission::whereIn('assignment_id', $assignmentIds)
            ->whereHas('grade')
            ->with('grade:id,submission_id,score,released_at')
            ->orderByDesc('attempt_no')
            ->get(['id', 'assignment_id', 'student_id', 'attempt_no']);

        // Pivot: [student_id][assignment_id] = {score, released}
        /** @var array<int, array<int, array{score: float, released: bool}>> $pivot */
        $pivot = [];

        foreach ($submissions as $sub) {
            $sid = $sub->student_id;
            $aid = $sub->assignment_id;

            // Keep only the highest-attempt graded submission per cell (first seen
            // wins because the query is already DESC by attempt_no).
            if (! isset($pivot[$sid][$aid])) {
                $pivot[$sid][$aid] = [
                    'score'    => (float) $sub->grade->score,
                    'released' => $sub->grade->released_at !== null,
                ];
            }
        }

        // Build one row per enrolled student, sorted alphabetically by name.
        $students = $enrollments->map(function (Enrollment $e) use ($assignmentIds, $pivot): array {
            $studentId = $e->student->id;
            /** @var array<int, array{score: float, released: bool}|null> $grades */
            $grades = [];
            $total  = 0.0;

            foreach ($assignmentIds as $aid) {
                $cell          = $pivot[$studentId][$aid] ?? null;
                $grades[$aid]  = $cell;
                $total        += $cell !== null ? $cell['score'] : 0.0;
            }

            return [
                'id'     => $studentId,
                'name'   => $e->student->name,
                'grades' => $grades,
                'total'  => $total,
            ];
        })->sortBy('name')->values();

        // Column averages across all graded submissions (including unreleased).
        /** @var array<int, float|null> $averages */
        $averages = [];

        foreach ($assignmentIds as $aid) {
            $scores = collect($pivot)
                ->map(fn (array $row): mixed => $row[$aid] ?? null)
                ->filter()
                ->pluck('score');

            $averages[$aid] = $scores->isNotEmpty()
                ? round((float) $scores->avg(), 2)
                : null;
        }

        return compact('section', 'assignments', 'students', 'averages');
    }
}
