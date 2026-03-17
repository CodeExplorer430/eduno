<?php

declare(strict_types=1);

use App\Domain\Assignment\Actions\UpdateAssignment;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;

it('updates assignment fields and returns fresh instance', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'ASN202',
        'title' => 'Update Assignment Action Test',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'A',
        'instructor_id' => $instructor->id,
    ]);

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Original Title',
        'max_score' => 50,
        'allow_resubmission' => false,
    ]);

    $action = new UpdateAssignment;

    $updated = $action->execute($assignment, [
        'title' => 'Updated Title',
        'max_score' => 100,
        'allow_resubmission' => true,
    ]);

    expect($updated)->toBeInstanceOf(Assignment::class);
    expect($updated->title)->toBe('Updated Title');
    expect($updated->max_score)->toEqual(100);

    $this->assertDatabaseHas('assignments', [
        'id' => $assignment->id,
        'title' => 'Updated Title',
        'max_score' => 100,
    ]);
});
