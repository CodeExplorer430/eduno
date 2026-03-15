<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Course\Models\Enrollment;
use App\Domain\Module\Models\Lesson;
use App\Domain\Module\Models\Module;
use App\Models\User;

class LessonPolicy
{
    public function view(User $user, Lesson $lesson): bool
    {
        $module = $lesson->module;
        $section = $module->section;

        if ($user->isAdmin() || $user->id === $section->instructor_id) {
            return true;
        }

        if ($lesson->isPublished() && $module->isPublished()) {
            return Enrollment::where('user_id', $user->id)
                ->where('course_section_id', $module->course_section_id)
                ->where('status', 'active')
                ->exists();
        }

        return false;
    }

    public function create(User $user, Module $module): bool
    {
        return $user->isAdmin() || $user->id === $module->section->instructor_id;
    }

    public function update(User $user, Lesson $lesson): bool
    {
        return $user->isAdmin() || $user->id === $lesson->module->section->instructor_id;
    }

    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->isAdmin() || $user->id === $lesson->module->section->instructor_id;
    }

    public function publish(User $user, Lesson $lesson): bool
    {
        return $user->isAdmin() || $user->id === $lesson->module->section->instructor_id;
    }
}
