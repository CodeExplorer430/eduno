<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Models\User;

class CourseSectionPolicy
{
    public function create(User $user, Course $course): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isInstructor() && $user->id === $course->created_by;
    }

    public function update(User $user, CourseSection $section): bool
    {
        return $user->isAdmin() || $user->id === $section->instructor_id;
    }

    public function delete(User $user, CourseSection $section): bool
    {
        return $user->isAdmin() || $user->id === $section->instructor_id;
    }
}
