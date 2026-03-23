<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Domain\Submission\Models\Grade;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GradeController extends Controller
{
    public function show(Grade $grade): Response
    {
        $this->authorize('view', $grade);

        $grade->load(['submission.assignment.courseSection.course']);

        return Inertia::render('Student/Grades/Show', [
            'grade' => [
                'id' => $grade->id,
                'score' => $grade->score,
                'feedback' => $grade->feedback,
                'released_at' => $grade->released_at?->toIso8601String(),
                'assignment' => [
                    'id' => $grade->submission->assignment->id,
                    'title' => $grade->submission->assignment->title,
                    'max_score' => $grade->submission->assignment->max_score,
                    'course_section_id' => $grade->submission->assignment->course_section_id,
                ],
            ],
        ]);
    }

    public function index(Request $request): Response
    {
        $grades = $request->user()
            ->submissions()
            ->with(['assignment.courseSection.course', 'grade'])
            ->whereHas('grade', fn ($q) => $q->whereNotNull('released_at'))
            ->get()
            ->pluck('grade')
            ->filter();

        return Inertia::render('Student/Grades/Index', [
            'grades' => $grades->values(),
        ]);
    }
}
