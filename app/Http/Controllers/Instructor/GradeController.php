<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Domain\Grade\Actions\GradeSubmission;
use App\Domain\Grade\Actions\ReleaseGrade;
use App\Domain\Grade\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Grade\GradeSubmissionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function __construct(
        private readonly GradeSubmission $gradeSubmission,
        private readonly ReleaseGrade $releaseGrade,
    ) {}

    public function store(GradeSubmissionRequest $request, Submission $submission): RedirectResponse
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('create', [Grade::class, $submission]);

        $this->gradeSubmission->execute(
            $request->user(),
            $submission,
            (float) $request->validated('score'),
            $request->validated('feedback'),
        );

        return back()->with('success', 'Grade saved.');
    }

    public function release(Request $request, Grade $grade): RedirectResponse
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('update', $grade);

        $this->releaseGrade->execute($request->user(), $grade);

        return back()->with('success', 'Grade released to student.');
    }
}
