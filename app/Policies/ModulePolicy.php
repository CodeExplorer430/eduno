<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Module\Models\Module;
use App\Models\User;

class ModulePolicy
{
    public function viewAny(User $user, CourseSection $section): bool
    {
        return true;
    }

    public function view(User $user, Module $module): bool
    {
        if ($user->isAdmin() || $user->id === $module->section->instructor_id) {
            return true;
        }

        if ($module->isPublished()) {
            return Enrollment::where('user_id', $user->id)
                ->where('course_section_id', $module->course_section_id)
                ->where('status', 'active')
                ->exists();
        }

        return false;
    }

    public function create(User $user, CourseSection $section): bool
    {
        return $user->isAdmin() || $user->id === $section->instructor_id;
    }

    public function update(User $user, Module $module): bool
    {
        return $user->isAdmin() || $user->id === $module->section->instructor_id;
    }

    public function delete(User $user, Module $module): bool
    {
        return $user->isAdmin() || $user->id === $module->section->instructor_id;
    }

    public function publish(User $user, Module $module): bool
    {
        return $user->isAdmin() || $user->id === $module->section->instructor_id;
    }
}
