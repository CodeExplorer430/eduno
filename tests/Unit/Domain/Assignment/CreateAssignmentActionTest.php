<?php

declare(strict_types=1);

use App\Domain\Assignment\Actions\CreateAssignment;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;

function makeAssignmentTestSection(): CourseSection
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'UAS'.fake()->unique()->numberBetween(100, 999),
        'title' => 'Test Course',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    return CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'Section A',
        'instructor_id' => $instructor->id,
    ]);
}

test('it creates an assignment with defaults for max_score and allow_resubmission', function (): void {
    $section = makeAssignmentTestSection();
    $action = new CreateAssignment;

    $assignment = $action->handle($section, ['title' => 'Default Assignment']);

    expect($assignment)->toBeInstanceOf(Assignment::class)
        ->and($assignment->max_score)->toBe('100.00')
        ->and($assignment->allow_resubmission)->toBeFalse()
        ->and($assignment->published_at)->toBeNull();
});

test('it respects explicit due_at', function (): void {
    $section = makeAssignmentTestSection();
    $action = new CreateAssignment;
    $due = now()->addWeek();

    $assignment = $action->handle($section, [
        'title' => 'Assignment with Due Date',
        'due_at' => $due->toDateTimeString(),
    ]);

    expect($assignment->due_at)->not->toBeNull();
});

test('it respects custom max_score', function (): void {
    $section = makeAssignmentTestSection();
    $action = new CreateAssignment;

    $assignment = $action->handle($section, [
        'title' => 'Custom Score Assignment',
        'max_score' => 50,
    ]);

    expect($assignment->max_score)->toBe('50.00');
});

test('it enables allow_resubmission when set', function (): void {
    $section = makeAssignmentTestSection();
    $action = new CreateAssignment;

    $assignment = $action->handle($section, [
        'title' => 'Resubmission Assignment',
        'allow_resubmission' => true,
    ]);

    expect($assignment->allow_resubmission)->toBeTrue();
});

test('it persists the assignment in the database', function (): void {
    $section = makeAssignmentTestSection();
    $action = new CreateAssignment;

    $action->handle($section, ['title' => 'Stored Assignment']);

    $this->assertDatabaseHas('assignments', [
        'course_section_id' => $section->id,
        'title' => 'Stored Assignment',
    ]);
});
