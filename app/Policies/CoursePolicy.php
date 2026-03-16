<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Course\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Course $course): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    public function update(User $user, Course $course): bool
    {
        return $user->isAdmin() || $course->created_by === $user->id;
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->isAdmin();
    }
}
