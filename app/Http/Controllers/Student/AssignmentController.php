<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Domain\Assignment\Models\Assignment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssignmentController extends Controller
{
    public function index(Request $request): Response
    {
        $sectionIds = $request->user()
            ->enrollments()
            ->pluck('course_section_id');

        $assignments = Assignment::whereIn('course_section_id', $sectionIds)
            ->whereNotNull('published_at')
            ->with('courseSection.course')
            ->orderBy('due_at')
            ->get();

        return Inertia::render('Student/Assignments/Index', [
            'assignments' => $assignments,
        ]);
    }

    public function show(Request $request, Assignment $assignment): Response
    {
        $this->authorize('view', $assignment);

        $assignment->load(['courseSection.course']);

        $submission = $request->user()
            ->submissions()
            ->where('assignment_id', $assignment->id)
            ->with('files', 'grade')
            ->latest()
            ->first();

        return Inertia::render('Student/Assignments/Show', [
            'assignment' => $assignment,
            'submission' => $submission,
        ]);
    }
}
