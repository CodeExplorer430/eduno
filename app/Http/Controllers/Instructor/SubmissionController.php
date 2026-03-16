<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Submission\Models\Submission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubmissionController extends Controller
{
    public function index(Request $request, Assignment $assignment): Response
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('update', $assignment);

        $submissions = $assignment->submissions()
            ->with(['student', 'files', 'grade'])
            ->get();

        return Inertia::render('Instructor/Submissions/Index', [
            'assignment' => $assignment->load('courseSection.course'),
            'submissions' => $submissions,
        ]);
    }

    public function show(Request $request, Submission $submission): Response
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('view', $submission);

        $submission->load(['assignment.courseSection.course', 'student', 'files', 'grade']);

        return Inertia::render('Instructor/Submissions/Show', [
            'submission' => $submission,
        ]);
    }
}
