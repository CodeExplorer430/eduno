<?php

declare(strict_types=1);

use App\Domain\Course\Actions\CreateCourseSection;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;

test('it creates a section with correct fields', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'CS101',
        'title' => 'Test Course',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
        'status' => 'draft',
        'created_by' => $instructor->id,
    ]);

    $action = new CreateCourseSection;

    $section = $action->handle($course, $instructor, [
        'section_name' => 'Section A',
        'schedule_text' => 'MWF 9:00-10:00',
    ]);

    expect($section)->toBeInstanceOf(CourseSection::class)
        ->and($section->course_id)->toBe($course->id)
        ->and($section->section_name)->toBe('Section A')
        ->and($section->schedule_text)->toBe('MWF 9:00-10:00');
});

test('it sets instructor_id from the provided user', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'CS102',
        'title' => 'Test Course',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
        'status' => 'draft',
        'created_by' => $instructor->id,
    ]);

    $action = new CreateCourseSection;

    $section = $action->handle($course, $instructor, ['section_name' => 'Section B']);

    expect($section->instructor_id)->toBe($instructor->id);
});

test('schedule_text is optional and defaults to null', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'CS103',
        'title' => 'Test Course',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
        'status' => 'draft',
        'created_by' => $instructor->id,
    ]);

    $action = new CreateCourseSection;

    $section = $action->handle($course, $instructor, ['section_name' => 'Section C']);

    expect($section->schedule_text)->toBeNull();
});

test('it stores the section in the database', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'CS104',
        'title' => 'Test Course',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
        'status' => 'draft',
        'created_by' => $instructor->id,
    ]);

    $action = new CreateCourseSection;
    $action->handle($course, $instructor, ['section_name' => 'Section D']);

    $this->assertDatabaseHas('course_sections', [
        'course_id' => $course->id,
        'section_name' => 'Section D',
        'instructor_id' => $instructor->id,
    ]);
});
