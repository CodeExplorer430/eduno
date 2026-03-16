<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Content\Models\Resource;
use App\Models\User;

class ResourcePolicy
{
    public function create(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    public function delete(User $user, Resource $resource): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isInstructor()
            && $resource->lesson->module->courseSection->instructor_id === $user->id;
    }
}
