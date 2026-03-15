<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Submission\Actions\GradeSubmission;
use App\Domain\Submission\Actions\ReleaseGrade;
use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Http\Requests\Submission\StoreGradeRequest;
use App\Http\Requests\Submission\UpdateGradeRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class GradeController extends Controller
{
    public function store(
        StoreGradeRequest $request,
        Submission $submission,
        GradeSubmission $action,
    ): RedirectResponse {
        $this->authorize('create', [Grade::class, $submission]);

        /** @var User $user */
        $user = auth()->user();

        $action->handle($submission, $user, $request->validated());

        return redirect()->route('submissions.show', $submission)
            ->with('success', 'Grade saved successfully.');
    }

    public function update(
        UpdateGradeRequest $request,
        Grade $grade,
        GradeSubmission $action,
    ): RedirectResponse {
        $this->authorize('update', $grade);

        /** @var User $user */
        $user = auth()->user();

        $action->handle($grade->submission, $user, $request->validated());

        return redirect()->route('submissions.show', $grade->submission_id)
            ->with('success', 'Grade updated successfully.');
    }

    public function release(Grade $grade, ReleaseGrade $action): RedirectResponse
    {
        $this->authorize('release', $grade);

        $action->handle($grade);

        return redirect()->route('submissions.show', $grade->submission_id)
            ->with('success', 'Grade released to student.');
    }
}
