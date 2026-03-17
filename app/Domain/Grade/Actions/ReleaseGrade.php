<?php

declare(strict_types=1);

namespace App\Domain\Grade\Actions;

use App\Domain\Audit\Actions\LogAction;
use App\Domain\Grade\Models\Grade;
use App\Jobs\NotifyStudentGradeReleased;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReleaseGrade
{
    public function __construct(private readonly LogAction $logAction) {}

    public function execute(User $actor, Grade $grade): Grade
    {
        $released = DB::transaction(function () use ($actor, $grade): Grade {
            $grade->update(['released_at' => now()]);

            $this->logAction->execute(
                $actor->id,
                'grade.released',
                Grade::class,
                $grade->id,
                ['submission_id' => $grade->submission_id],
            );

            return $grade->fresh();
        });

        NotifyStudentGradeReleased::dispatch($released);

        return $released;
    }
}
