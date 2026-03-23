<?php

declare(strict_types=1);

namespace App\Domain\Grade\Actions;

use App\Domain\Audit\Actions\LogAction;
use App\Domain\Submission\Models\Grade;
use App\Jobs\NotifyStudentGradeReleased;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ReleaseGrade
{
    public function __construct(private readonly LogAction $logAction)
    {
    }

    public function execute(User $actor, Grade $grade): Grade
    {
        $grade->update(['released_at' => now()]);

        Cache::forget('report.admin');

        NotifyStudentGradeReleased::dispatch($grade);

        $this->logAction->execute(
            $actor->id,
            'grade.released',
            Grade::class,
            $grade->id,
            ['submission_id' => $grade->submission_id],
        );

        return $grade->fresh();
    }
}
