<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Assignment\Models\Assignment;
use App\Models\User;

class AssignmentPolicy
{
    public function view(User $user, Assignment $assignment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->isInstructor() && $assignment->courseSection->instructor_id === $user->id) {
            return true;
        }
        if ($user->isStudent()) {
            return $user->enrollments()->where('course_section_id', $assignment->course_section_id)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    public function update(User $user, Assignment $assignment): bool
    {
        return $user->isAdmin() || ($user->isInstructor() && $assignment->courseSection->instructor_id === $user->id);
    }

    public function delete(User $user, Assignment $assignment): bool
    {
        return $user->isAdmin() || ($user->isInstructor() && $assignment->courseSection->instructor_id === $user->id);
    }
}
