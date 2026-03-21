<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Submission\Actions\SubmitAssignment;
use App\Domain\Submission\Models\Submission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Submission\UploadSubmissionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubmissionController extends Controller
{
    public function __construct(private readonly SubmitAssignment $submitAssignment)
    {
    }

    public function create(Request $request, Assignment $assignment): Response
    {
        $this->authorize('create', Submission::class);
        abort_unless(
            $request->user()->enrollments()->where('course_section_id', $assignment->course_section_id)->exists(),
            403
        );

        $assignment->load('courseSection.course');

        return Inertia::render('Student/Submissions/Create', [
            'assignment' => $assignment,
        ]);
    }

    public function store(UploadSubmissionRequest $request, Assignment $assignment): RedirectResponse
    {
        $this->authorize('create', Submission::class);
        abort_unless(
            $request->user()->enrollments()->where('course_section_id', $assignment->course_section_id)->exists(),
            403
        );

        $submission = $this->submitAssignment->handle(
            $assignment,
            $request->user(),
            $request->file('files') ?? []
        );

        return redirect()->route('student.submissions.show', $submission);
    }

    public function show(Request $request, Submission $submission): Response
    {
        $this->authorize('view', $submission);

        $submission->load(['assignment.courseSection.course', 'files', 'grade']);

        return Inertia::render('Student/Submissions/Show', [
            'submission' => $submission,
        ]);
    }
}
