<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Announcement\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    public function view(User $user, Announcement $announcement): bool
    {
        if ($user->isAdmin() || $user->isInstructor()) {
            return true;
        }

        return $user->enrollments()->where('course_section_id', $announcement->course_section_id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    public function update(User $user, Announcement $announcement): bool
    {
        return $user->isAdmin() || ($user->isInstructor() && $announcement->created_by === $user->id);
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->isAdmin() || ($user->isInstructor() && $announcement->created_by === $user->id);
    }
}
