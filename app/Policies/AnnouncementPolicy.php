<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Announcement\Models\Announcement;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Models\User;

class AnnouncementPolicy
{
    public function viewAny(User $user, CourseSection $section): bool
    {
        return true;
    }

    public function view(User $user, Announcement $announcement): bool
    {
        if ($user->isAdmin() || $user->id === $announcement->section->instructor_id) {
            return true;
        }

        if ($announcement->isPublished()) {
            return Enrollment::where('user_id', $user->id)
                ->where('course_section_id', $announcement->course_section_id)
                ->where('status', 'active')
                ->exists();
        }

        return false;
    }

    public function create(User $user, CourseSection $section): bool
    {
        return $user->isAdmin() || $user->id === $section->instructor_id;
    }

    public function update(User $user, Announcement $announcement): bool
    {
        return $user->isAdmin() || $user->id === $announcement->section->instructor_id;
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->isAdmin() || $user->id === $announcement->section->instructor_id;
    }

    public function publish(User $user, Announcement $announcement): bool
    {
        return $user->isAdmin() || $user->id === $announcement->section->instructor_id;
    }
}
