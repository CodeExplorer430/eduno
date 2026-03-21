<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Course\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LessonPolicy
{
    public function view(User $user, Model $lesson): bool
    {
        $module = $lesson->module; /** @phpstan-ignore-line */
        $section = $module->courseSection ?? $module->section ?? null;

        if ($user->isAdmin() || $user->id === $section?->instructor_id) {
            return true;
        }

        if ($lesson->isPublished() && $module->isPublished()) { /** @phpstan-ignore-line */
            return Enrollment::where('user_id', $user->id)
                ->where('course_section_id', $module->course_section_id)
                ->where('status', 'active')
                ->exists();
        }

        return false;
    }

    public function create(User $user, ?Model $module = null): bool
    {
        if ($module === null) {
            return $user->isAdmin() || $user->isInstructor();
        }

        $section = $module->courseSection ?? $module->section ?? null;

        return $user->isAdmin() || $user->id === $section?->instructor_id;
    }

    public function update(User $user, Model $lesson): bool
    {
        return $user->isAdmin() || $user->id === ($lesson->module->courseSection ?? $lesson->module->section ?? null)?->instructor_id;
    }

    public function delete(User $user, Model $lesson): bool
    {
        return $user->isAdmin() || $user->id === ($lesson->module->courseSection ?? $lesson->module->section ?? null)?->instructor_id;
    }

    public function publish(User $user, Model $lesson): bool
    {
        return $user->isAdmin() || $user->id === ($lesson->module->courseSection ?? $lesson->module->section ?? null)?->instructor_id;
    }
}
