<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasRole(UserRole::Admin);
    }

    public function view(User $authUser, User $targetUser): bool
    {
        return $authUser->hasRole(UserRole::Admin) || $authUser->id === $targetUser->id;
    }

    public function update(User $authUser, User $targetUser): bool
    {
        return $authUser->hasRole(UserRole::Admin) || $authUser->id === $targetUser->id;
    }

    public function delete(User $authUser, User $targetUser): bool
    {
        return $authUser->hasRole(UserRole::Admin) && $authUser->id !== $targetUser->id;
    }

    public function impersonate(User $authUser): bool
    {
        return $authUser->hasRole(UserRole::Admin);
    }
}
