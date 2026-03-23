<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Models\User;

class GradePolicy
{
    public function view(User $user, Grade $grade): bool
    {
        return $grade->submission->student_id === $user->id
            && $grade->released_at !== null;
    }

    public function create(User $user, Submission $submission): bool
    {
        return $user->isAdmin()
            || $user->id === $submission->assignment->section->instructor_id;
    }

    public function update(User $user, Grade $grade): bool
    {
        return $user->isAdmin()
            || $user->id === $grade->submission->assignment->section->instructor_id;
    }

    public function release(User $user, Grade $grade): bool
    {
        return $user->isAdmin()
            || $user->id === $grade->submission->assignment->section->instructor_id;
    }
}
