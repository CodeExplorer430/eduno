<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Grade\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Models\User;

class GradePolicy
{
    public function create(User $user, Submission $submission): bool
    {
        return $user->isAdmin() || ($user->isInstructor() && $submission->assignment->courseSection->instructor_id === $user->id);
    }

    public function update(User $user, Grade $grade): bool
    {
        return $user->isAdmin() || ($user->isInstructor() && $grade->submission->assignment->courseSection->instructor_id === $user->id);
    }

    public function view(User $user, Grade $grade): bool
    {
        if ($user->isAdmin() || $user->isInstructor()) {
            return true;
        }

        return $user->isStudent() && $grade->submission->student_id === $user->id && $grade->released_at !== null;
    }
}
