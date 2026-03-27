<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Domain\Course\Models\CourseSection;
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
        /** @var \Illuminate\Database\Eloquent\Collection<int, Grade> $gradeModels */
        $gradeModels = Grade::query()
            ->whereHas('submission', fn ($q) => $q->where('student_id', $request->user()->id))
            ->with(['submission.assignment.section.course'])
            ->whereNotNull('released_at')
            ->latest('released_at')
            ->get();

        $grades = $gradeModels->map(function (Grade $grade): array {
            /** @var CourseSection $section */
            $section = $grade->submission->assignment->section;

            return [
                'id'          => $grade->id,
                'score'       => $grade->score,
                'feedback'    => $grade->feedback,
                'released_at' => $grade->released_at?->toIso8601String(),
                'submission'  => [
                    'assignment' => [
                        'title'          => $grade->submission->assignment->title,
                        'max_score'      => $grade->submission->assignment->max_score,
                        'course_section' => [
                            'section_name' => $section->section_name,
                            'course'       => [
                                'title' => $section->course?->title,
                            ],
                        ],
                    ],
                ],
            ];
        });

        return Inertia::render('Student/Grades/Index', [
            'grades' => $grades->values(),
        ]);
    }
}
