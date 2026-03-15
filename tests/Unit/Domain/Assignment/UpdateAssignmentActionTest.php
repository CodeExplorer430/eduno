<?php

declare(strict_types=1);

use App\Domain\Assignment\Actions\UpdateAssignment;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;

function makeAssignmentForUpdate(): Assignment
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'UA'.fake()->unique()->numberBetween(100, 999),
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
        'title' => 'Original Assignment',
        'instructions' => 'Old instructions',
        'max_score' => 100,
        'allow_resubmission' => false,
        'due_at' => now()->addDays(7),
    ]);
}

test('it updates title and instructions', function (): void {
    $assignment = makeAssignmentForUpdate();
    $action = new UpdateAssignment();

    $updated = $action->handle($assignment, [
        'title' => 'Updated Assignment',
        'instructions' => 'New instructions',
        'max_score' => 100,
    ]);

    expect($updated->title)->toBe('Updated Assignment')
        ->and($updated->instructions)->toBe('New instructions');
});

test('it updates due_at', function (): void {
    $assignment = makeAssignmentForUpdate();
    $action = new UpdateAssignment();
    $newDue = now()->addDays(14)->toDateTimeString();

    $updated = $action->handle($assignment, [
        'title' => 'Assignment',
        'due_at' => $newDue,
        'max_score' => 100,
    ]);

    expect($updated->due_at)->not->toBeNull();
});

test('it updates max_score', function (): void {
    $assignment = makeAssignmentForUpdate();
    $action = new UpdateAssignment();

    $updated = $action->handle($assignment, [
        'title' => 'Assignment',
        'max_score' => 50,
    ]);

    expect((float) $updated->max_score)->toBe(50.0);
});

test('it persists changes to the database', function (): void {
    $assignment = makeAssignmentForUpdate();
    $action = new UpdateAssignment();

    $action->handle($assignment, ['title' => 'Saved Title', 'max_score' => 80]);

    $this->assertDatabaseHas('assignments', [
        'id' => $assignment->id,
        'title' => 'Saved Title',
    ]);
});
