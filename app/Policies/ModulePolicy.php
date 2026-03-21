<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ModulePolicy
{
    public function viewAny(User $user, CourseSection $section): bool
    {
        return true;
    }

    public function view(User $user, Model $module): bool
    {
        $section = $module->courseSection ?? $module->section ?? null;

        if ($user->isAdmin() || $user->id === $section?->instructor_id) {
            return true;
        }

        if ($module->isPublished()) { /** @phpstan-ignore-line */
            return Enrollment::where('user_id', $user->id)
                ->where('course_section_id', $module->course_section_id) /** @phpstan-ignore-line */
                ->where('status', 'active')
                ->exists();
        }

        return false;
    }

    public function create(User $user, ?CourseSection $section = null): bool
    {
        if ($section === null) {
            return $user->isAdmin() || $user->isInstructor();
        }

        return $user->isAdmin() || $user->id === $section->instructor_id;
    }

    public function update(User $user, Model $module): bool
    {
        return $user->isAdmin() || $user->id === ($module->courseSection ?? $module->section ?? null)?->instructor_id;
    }

    public function delete(User $user, Model $module): bool
    {
        return $user->isAdmin() || $user->id === ($module->courseSection ?? $module->section ?? null)?->instructor_id;
    }

    public function publish(User $user, Model $module): bool
    {
        return $user->isAdmin() || $user->id === ($module->courseSection ?? $module->section ?? null)?->instructor_id;
    }
}
