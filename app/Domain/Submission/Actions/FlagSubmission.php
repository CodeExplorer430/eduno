<?php

declare(strict_types=1);

namespace App\Domain\Submission\Actions;

use App\Domain\Submission\Models\Submission;

class FlagSubmission
{
    public function handle(Submission $submission): void
    {
        $submission->update(['flagged_for_review' => ! $submission->flagged_for_review]);
    }
}
