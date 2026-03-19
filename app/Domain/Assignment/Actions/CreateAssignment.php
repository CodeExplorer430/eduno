<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Actions;

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\CourseSection;

class CreateAssignment
{
    public function handle(CourseSection $section, array $data): Assignment
    {
        return Assignment::create([
            'course_section_id' => $section->id,
            'title' => $data['title'],
            'instructions' => $data['instructions'] ?? null,
            'due_at' => $data['due_at'] ?? null,
            'max_score' => $data['max_score'] ?? 100,
            'allow_resubmission' => $data['allow_resubmission'] ?? false,
        ]);
    }
}
