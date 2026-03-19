<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Actions;

use App\Domain\Assignment\Models\Assignment;

class UpdateAssignment
{
    public function handle(Assignment $assignment, array $data): Assignment
    {
        $assignment->update([
            'title' => $data['title'],
            'instructions' => $data['instructions'] ?? null,
            'due_at' => $data['due_at'] ?? null,
            'max_score' => $data['max_score'] ?? $assignment->max_score,
            'allow_resubmission' => $data['allow_resubmission'] ?? $assignment->allow_resubmission,
        ]);

        return $assignment;
    }
}
