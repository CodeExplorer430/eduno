<?php

declare(strict_types=1);

namespace App\Domain\Grade\Actions;

use App\Domain\Audit\Actions\LogAction;
use App\Domain\Grade\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GradeSubmission
{
    public function __construct(private readonly LogAction $logAction) {}

    public function execute(User $grader, Submission $submission, float $score, ?string $feedback): Grade
    {
        return DB::transaction(function () use ($grader, $submission, $score, $feedback): Grade {
            $grade = Grade::updateOrCreate(
                ['submission_id' => $submission->id],
                [
                    'graded_by' => $grader->id,
                    'score' => $score,
                    'feedback' => $feedback,
                ],
            );

            $this->logAction->execute(
                $grader->id,
                'grade.updated',
                Submission::class,
                $submission->id,
                ['score' => $score],
            );

            return $grade;
        });
    }
}
