<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Submission\Models\Submission;
use App\Enums\SubmissionStatus;
use App\Models\User;

class SubmissionPolicy
{
    public function viewAny(User $user, Assignment $assignment): bool
    {
        return $user->isAdmin() || $user->id === $assignment->section->instructor_id;
    }

    public function view(User $user, Submission $submission): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id === $submission->assignment->section->instructor_id) {
            return true;
        }

        return $user->id === $submission->student_id;
    }

    public function create(User $user, ?Assignment $assignment = null): bool
    {
        if ($assignment === null) {
            return $user->isStudent();
        }

        if (! $assignment->isPublished()) {
            return false;
        }

        if (! $assignment->allow_resubmission) {
            $alreadySubmitted = Submission::where('assignment_id', $assignment->id)
                ->where('student_id', $user->id)
                ->exists();

            if ($alreadySubmitted) {
                return false;
            }
        }

        return Enrollment::where('user_id', $user->id)
            ->where('course_section_id', $assignment->course_section_id)
            ->where('status', 'active')
            ->exists();
    }

    public function delete(User $user, Submission $submission): bool
    {
        return $user->id === $submission->student_id
            && $submission->status === SubmissionStatus::Submitted;
    }
}
