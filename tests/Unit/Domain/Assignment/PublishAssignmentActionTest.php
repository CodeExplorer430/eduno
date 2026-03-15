<?php

declare(strict_types=1);

use App\Domain\Assignment\Actions\PublishAssignment;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;

function makeAssignmentForPublish(bool $published = false): Assignment
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'PA'.fake()->unique()->numberBetween(100, 999),
        'title' => 'Test Course',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);
    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'Section A',
        'instructor_id' => $instructor->id,
    ]);

    return Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Test Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => $published ? now()->subMinute() : null,
    ]);
}

test('it sets published_at when publishing', function (): void {
    $assignment = makeAssignmentForPublish(false);
    $action = new PublishAssignment;

    $result = $action->handle($assignment);

    expect($result->published_at)->not->toBeNull();
});

test('it clears published_at when unpublishing', function (): void {
    $assignment = makeAssignmentForPublish(true);
    $action = new PublishAssignment;

    $result = $action->handle($assignment);

    expect($result->published_at)->toBeNull();
});

test('it persists publish state to the database', function (): void {
    $assignment = makeAssignmentForPublish(false);
    $action = new PublishAssignment;

    $action->handle($assignment);

    expect(Assignment::find($assignment->id)->published_at)->not->toBeNull();
});
