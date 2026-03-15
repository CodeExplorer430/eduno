<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Actions;

use App\Domain\Assignment\Models\Assignment;

class PublishAssignment
{
    public function handle(Assignment $assignment): Assignment
    {
        $assignment->update([
            'published_at' => $assignment->published_at === null ? now() : null,
        ]);

        return $assignment;
    }
}
