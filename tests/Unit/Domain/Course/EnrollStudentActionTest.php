<?php

declare(strict_types=1);

use App\Domain\Course\Actions\EnrollStudent;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Validation\ValidationException;

function makeSection(): CourseSection
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'ENR'.fake()->unique()->numberBetween(100, 999),
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

test('it enrolls a student in a section', function (): void {
    $student = User::factory()->create(['role' => UserRole::Student]);
    $section = makeSection();
    $action = new EnrollStudent;

    $enrollment = $action->handle($student, $section);

    expect($enrollment)->toBeInstanceOf(Enrollment::class)
        ->and($enrollment->user_id)->toBe($student->id)
        ->and($enrollment->course_section_id)->toBe($section->id)
        ->and($enrollment->status)->toBe('active');
});

test('it persists the enrollment in the database', function (): void {
    $student = User::factory()->create(['role' => UserRole::Student]);
    $section = makeSection();
    $action = new EnrollStudent;

    $action->handle($student, $section);

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
    ]);
});

test('it throws a validation exception when student is already enrolled', function (): void {
    $student = User::factory()->create(['role' => UserRole::Student]);
    $section = makeSection();
    $action = new EnrollStudent;

    $action->handle($student, $section);

    expect(fn () => $action->handle($student, $section))
        ->toThrow(ValidationException::class);
});

test('different students can enroll in the same section', function (): void {
    $studentA = User::factory()->create(['role' => UserRole::Student]);
    $studentB = User::factory()->create(['role' => UserRole::Student]);
    $section = makeSection();
    $action = new EnrollStudent;

    $enrollmentA = $action->handle($studentA, $section);
    $enrollmentB = $action->handle($studentB, $section);

    expect($enrollmentA->id)->not->toBe($enrollmentB->id);
    $this->assertDatabaseCount('enrollments', 2);
});
