<?php

declare(strict_types=1);

namespace App\Domain\Submission\Actions;

use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Enums\SubmissionStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GradeSubmission
{
    public function handle(Submission $submission, User $grader, array $data): Grade
    {
        return DB::transaction(function () use ($submission, $grader, $data): Grade {
            $existing = Grade::where('submission_id', $submission->id)->first();

            $action = $existing === null ? 'grade.created' : 'grade.updated';

            /** @var Grade $grade */
            $grade = Grade::updateOrCreate(
                ['submission_id' => $submission->id],
                [
                    'graded_by' => $grader->id,
                    'score' => $data['score'],
                    'feedback' => $data['feedback'] ?? null,
                ]
            );

            $submission->update(['status' => SubmissionStatus::Graded]);

            DB::table('audit_logs')->insert([
                'actor_id' => $grader->id,
                'action' => $action,
                'entity_type' => Grade::class,
                'entity_id' => $grade->id,
                'metadata' => json_encode([
                    'score' => $grade->score,
                    'submission_id' => $grade->submission_id,
                ]),
                'created_at' => now(),
            ]);

            return $grade;
        });
    }
}
