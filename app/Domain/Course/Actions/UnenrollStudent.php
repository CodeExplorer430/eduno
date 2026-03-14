<?php

declare(strict_types=1);

namespace App\Domain\Course\Actions;

use App\Domain\Course\Models\Enrollment;

class UnenrollStudent
{
    public function handle(Enrollment $enrollment): void
    {
        $enrollment->update(['status' => 'withdrawn']);
    }
}
