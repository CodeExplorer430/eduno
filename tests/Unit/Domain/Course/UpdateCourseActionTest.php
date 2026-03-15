<?php

declare(strict_types=1);

use App\Domain\Course\Actions\UpdateCourse;
use App\Domain\Course\Models\Course;
use App\Enums\CourseStatus;
use App\Enums\UserRole;
use App\Models\User;

test('it updates mutable course fields', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'CS101',
        'title' => 'Original Title',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
        'status' => 'draft',
        'created_by' => $instructor->id,
    ]);

    $action = new UpdateCourse;

    $updated = $action->handle($course, [
        'code' => 'CS101',
        'title' => 'Updated Title',
        'description' => 'A new description',
        'department' => 'IT',
        'term' => '2nd Semester',
        'academic_year' => '2026-2027',
    ], $instructor);

    expect($updated->title)->toBe('Updated Title')
        ->and($updated->description)->toBe('A new description')
        ->and($updated->department)->toBe('IT')
        ->and($updated->term)->toBe('2nd Semester')
        ->and($updated->academic_year)->toBe('2026-2027');
});

test('it updates course status when provided', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'CS201',
        'title' => 'Test Course',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
        'status' => 'draft',
        'created_by' => $instructor->id,
    ]);

    $action = new UpdateCourse;

    $updated = $action->handle($course, [
        'code' => 'CS201',
        'title' => 'Test Course',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
        'status' => 'published',
    ], $instructor);

    expect($updated->status)->toBe(CourseStatus::Published);
});

test('it persists changes to the database', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'CS301',
        'title' => 'Original',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
        'status' => 'draft',
        'created_by' => $instructor->id,
    ]);

    $action = new UpdateCourse;
    $action->handle($course, [
        'code' => 'CS301',
        'title' => 'Persisted Title',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
    ], $instructor);

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'title' => 'Persisted Title',
    ]);
});

test('description is optional and can be cleared to null', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'CS401',
        'title' => 'Test',
        'description' => 'Old description',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
        'status' => 'draft',
        'created_by' => $instructor->id,
    ]);

    $action = new UpdateCourse;
    $updated = $action->handle($course, [
        'code' => 'CS401',
        'title' => 'Test',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
    ], $instructor);

    expect($updated->description)->toBeNull();
});
