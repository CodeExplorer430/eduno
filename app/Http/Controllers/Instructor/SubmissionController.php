<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function export(Request $request, Assignment $assignment): StreamedResponse
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('export', $assignment);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="submissions-'.$assignment->id.'.csv"',
        ];

        $callback = function () use ($assignment): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['ID', 'Student', 'Assignment', 'Submitted At', 'Is Late', 'Score', 'Released']);

            $assignment->submissions()
                ->with(['student', 'grade'])
                ->orderBy('submitted_at')
                ->chunk(200, function (\Illuminate\Database\Eloquent\Collection $submissions) use ($handle, $assignment): void {
                    /** @var Submission $sub */
                    foreach ($submissions as $sub) {
                        $grade = $sub->grade instanceof Grade ? $sub->grade : null;

                        fputcsv($handle, [
                            $sub->id,
                            $sub->student->name,
                            $assignment->title,
                            (string) $sub->submitted_at,
                            $sub->is_late ? 'Yes' : 'No',
                            $grade !== null ? (string) $grade->score : '',
                            $grade !== null && $grade->released_at !== null ? (string) $grade->released_at : '',
                        ]);
                    }
                });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
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
