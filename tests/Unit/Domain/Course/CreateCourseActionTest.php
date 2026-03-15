<?php

declare(strict_types=1);

use App\Domain\Course\Actions\CreateCourse;
use App\Domain\Course\Models\Course;
use App\Enums\CourseStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Validation\ValidationException;

test('it creates a course with valid data', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $action = new CreateCourse();

    $course = $action->handle($instructor, [
        'code' => 'CS101',
        'title' => 'Introduction to Computer Science',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
    ]);

    expect($course)->toBeInstanceOf(Course::class)
        ->and($course->code)->toBe('CS101')
        ->and($course->title)->toBe('Introduction to Computer Science')
        ->and($course->created_by)->toBe($instructor->id)
        ->and($course->status)->toBe(CourseStatus::Draft);
});

test('it stores the course in the database', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $action = new CreateCourse();

    $action->handle($instructor, [
        'code' => 'CCS202',
        'title' => 'Data Structures',
        'department' => 'CCS',
        'term' => '2nd Semester',
        'academic_year' => '2025-2026',
    ]);

    $this->assertDatabaseHas('courses', [
        'code' => 'CCS202',
        'created_by' => $instructor->id,
    ]);
});

test('it throws a validation exception for duplicate course code', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    Course::create([
        'code' => 'CS101',
        'title' => 'Existing Course',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
        'status' => 'draft',
        'created_by' => $instructor->id,
    ]);

    $action = new CreateCourse();

    expect(fn () => $action->handle($instructor, [
        'code' => 'CS101',
        'title' => 'Duplicate Course',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
    ]))->toThrow(ValidationException::class);
});

test('description is optional and defaults to null', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $action = new CreateCourse();

    $course = $action->handle($instructor, [
        'code' => 'OPT100',
        'title' => 'Optional Description',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
    ]);

    expect($course->description)->toBeNull();
});
