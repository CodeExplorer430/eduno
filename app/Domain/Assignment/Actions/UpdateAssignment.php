<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Actions;

use App\Domain\Assignment\Models\Assignment;

class UpdateAssignment
{
    public function execute(Assignment $assignment, array $data): Assignment
    {
        $assignment->update($data);

        return $assignment->fresh();
    }
}
