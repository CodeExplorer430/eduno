<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Content\Models\Module;
use App\Models\User;

class ModulePolicy
{
    public function create(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    public function update(User $user, Module $module): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isInstructor()
            && $module->courseSection->instructor_id === $user->id;
    }

    public function delete(User $user, Module $module): bool
    {
        return $this->update($user, $module);
    }
}
