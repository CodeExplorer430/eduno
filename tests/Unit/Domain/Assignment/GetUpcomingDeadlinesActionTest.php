<?php

declare(strict_types=1);

use App\Domain\Assignment\Actions\GetUpcomingDeadlines;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Enums\UserRole;
use App\Models\User;

function makeDeadlineTestSetup(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'UGD'.fake()->unique()->numberBetween(100, 999),
        'title' => 'Deadline Test Course',
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
    $student = User::factory()->create(['role' => UserRole::Student]);

    return [$student, $section, $course];
}

test('returns empty collection when student has no enrollments', function (): void {
    [$student] = makeDeadlineTestSetup();
    $action = new GetUpcomingDeadlines();

    $result = $action->execute($student);

    expect($result)->toBeEmpty();
});

test('returns assignments due within the default 7-day window', function (): void {
    [$student, $section] = makeDeadlineTestSetup();

    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);

    Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Due Soon',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => now()->subMinute(),
        'due_at' => now()->addDays(3),
    ]);

    $action = new GetUpcomingDeadlines();
    $result = $action->execute($student);

    expect($result)->toHaveCount(1)
        ->and($result->first()->title)->toBe('Due Soon');
});

test('excludes assignments outside the window and unpublished assignments', function (): void {
    [$student, $section] = makeDeadlineTestSetup();

    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);

    // Outside default 7-day window
    Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Due Far Away',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => now()->subMinute(),
        'due_at' => now()->addDays(10),
    ]);

    // Unpublished — must not appear even if due within window
    Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Unpublished Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => null,
        'due_at' => now()->addDays(2),
    ]);

    $action = new GetUpcomingDeadlines();
    $result = $action->execute($student);

    expect($result)->toBeEmpty();
});
