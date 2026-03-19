<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Models\User;

class AssignmentPolicy
{
    public function viewAny(User $user, CourseSection $section): bool
    {
        return true;
    }

    public function view(User $user, Assignment $assignment): bool
    {
        if ($user->isAdmin() || $user->id === $assignment->section->instructor_id) {
            return true;
        }

        if ($assignment->isPublished()) {
            return Enrollment::where('user_id', $user->id)
                ->where('course_section_id', $assignment->course_section_id)
                ->where('status', 'active')
                ->exists();
        }

        return false;
    }

    public function create(User $user, CourseSection $section): bool
    {
        return $user->isAdmin() || $user->id === $section->instructor_id;
    }

    public function update(User $user, Assignment $assignment): bool
    {
        return $user->isAdmin() || $user->id === $assignment->section->instructor_id;
    }

    public function delete(User $user, Assignment $assignment): bool
    {
        return $user->isAdmin() || $user->id === $assignment->section->instructor_id;
    }

    public function publish(User $user, Assignment $assignment): bool
    {
        return $user->isAdmin() || $user->id === $assignment->section->instructor_id;
    }
}
