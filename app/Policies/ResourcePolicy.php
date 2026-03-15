<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Course\Models\Enrollment;
use App\Domain\Module\Models\Lesson;
use App\Domain\Module\Models\Resource;
use App\Enums\ResourceVisibility;
use App\Models\User;

class ResourcePolicy
{
    public function view(User $user, Resource $resource): bool
    {
        $section = $resource->lesson->module->section;

        if ($user->isAdmin() || $user->id === $section->instructor_id) {
            return true;
        }

        return match ($resource->visibility) {
            ResourceVisibility::Public => true,
            ResourceVisibility::Enrolled => Enrollment::where('user_id', $user->id)
                ->where('course_section_id', $section->id)
                ->where('status', 'active')
                ->exists(),
            ResourceVisibility::Instructor => false,
        };
    }

    public function create(User $user, Lesson $lesson): bool
    {
        return $user->isAdmin() || $user->id === $lesson->module->section->instructor_id;
    }

    public function delete(User $user, Resource $resource): bool
    {
        return $user->isAdmin() || $user->id === $resource->lesson->module->section->instructor_id;
    }
}
