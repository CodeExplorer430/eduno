<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Course\Models\Enrollment;
use App\Enums\ResourceVisibility;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ResourcePolicy
{
    public function view(User $user, Model $resource): bool
    {
        $section = $resource->lesson->module->courseSection
            ?? $resource->lesson->module->section
            ?? null;

        if ($user->isAdmin() || $user->id === $section?->instructor_id) {
            return true;
        }

        return match ($resource->visibility) { /** @phpstan-ignore-line */
            ResourceVisibility::Public => true,
            ResourceVisibility::Enrolled => Enrollment::where('user_id', $user->id)
                ->where('course_section_id', $section->id)
                ->where('status', 'active')
                ->exists(),
            ResourceVisibility::Instructor => false,
        };
    }

    public function create(User $user, ?Model $lesson = null): bool
    {
        if ($lesson === null) {
            return $user->isAdmin() || $user->isInstructor();
        }

        $section = $lesson->module->courseSection
            ?? $lesson->module->section
            ?? null;

        return $user->isAdmin() || $user->id === $section?->instructor_id;
    }

    public function delete(User $user, Model $resource): bool
    {
        return $user->isAdmin() || $user->id === ($resource->lesson->module->courseSection ?? $resource->lesson->module->section ?? null)?->instructor_id;
    }
}
