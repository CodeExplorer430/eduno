<?php

declare(strict_types=1);

namespace App\Domain\Submission\Actions;

use App\Domain\Submission\Models\Grade;
use App\Enums\SubmissionStatus;
use Illuminate\Support\Facades\DB;

class ReleaseGrade
{
    public function handle(Grade $grade): Grade
    {
        return DB::transaction(function () use ($grade): Grade {
            $grade->update(['released_at' => now()]);

            $grade->submission->update(['status' => SubmissionStatus::Returned]);

            DB::table('audit_logs')->insert([
                'actor_id' => $grade->graded_by,
                'action' => 'grade.released',
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
