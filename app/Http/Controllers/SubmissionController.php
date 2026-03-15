<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Submission\Actions\SubmitAssignment;
use App\Domain\Submission\Models\Submission;
use App\Http\Requests\Submission\StoreSubmissionRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SubmissionController extends Controller
{
    public function index(Assignment $assignment): Response
    {
        $this->authorize('viewAny', [Submission::class, $assignment]);

        /** @var User $user */
        $user = auth()->user();

        $assignment->load('section.course');

        $submissions = $assignment->submissions()
            ->with(['student', 'files', 'grade'])
            ->latest()
            ->get();

        $canManage = $user->isAdmin() || $user->id === $assignment->section->instructor_id;

        return Inertia::render('Submission/Index', [
            'assignment' => $assignment,
            'submissions' => $submissions,
            'canManage' => $canManage,
        ]);
    }

    public function store(
        StoreSubmissionRequest $request,
        Assignment $assignment,
        SubmitAssignment $action,
    ): RedirectResponse {
        $this->authorize('create', [Submission::class, $assignment]);

        /** @var User $user */
        $user = auth()->user();

        $submission = $action->handle($assignment, $user, $request->file('files', []));

        return redirect()->route('submissions.show', $submission)
            ->with('success', 'Submission uploaded successfully.');
    }

    public function show(Submission $submission): Response
    {
        $this->authorize('view', $submission);

        /** @var User $user */
        $user = auth()->user();

        $submission->load(['assignment.section.course', 'student', 'files', 'grade.gradedBy']);

        $isInstructor = $user->isAdmin()
            || $user->id === $submission->assignment->section->instructor_id;

        return Inertia::render('Submission/Show', [
            'submission' => $submission,
            'isInstructor' => $isInstructor,
        ]);
    }

    public function destroy(Submission $submission): RedirectResponse
    {
        $this->authorize('delete', $submission);

        $assignmentId = $submission->assignment_id;
        $submission->delete();

        return redirect()->route('assignments.show', $assignmentId)
            ->with('success', 'Submission deleted.');
    }
}
