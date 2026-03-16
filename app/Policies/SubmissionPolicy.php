<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Submission\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    public function create(User $user): bool
    {
        return $user->isStudent();
    }

    public function view(User $user, Submission $submission): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->isStudent() && $submission->student_id === $user->id) {
            return true;
        }
        if ($user->isInstructor()) {
            return $submission->assignment->courseSection->instructor_id === $user->id;
        }

        return false;
    }

    public function update(User $user, Submission $submission): bool
    {
        return $user->isAdmin() || ($user->isInstructor() && $submission->assignment->courseSection->instructor_id === $user->id);
    }
}
