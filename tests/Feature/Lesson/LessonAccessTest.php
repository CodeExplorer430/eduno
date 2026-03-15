<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Module\Models\Lesson;
use App\Domain\Module\Models\Module;
use App\Enums\UserRole;
use App\Models\User;

// ─── Helpers ─────────────────────────────────────────────────────────────────

function makeLessonAccessSetup(bool $lessonPublished, bool $modulePublished): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'LA'.fake()->unique()->numberBetween(100, 999),
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
    $module = Module::create([
        'course_section_id' => $section->id,
        'title' => 'Test Module',
        'order_no' => 1,
        'published_at' => $modulePublished ? now()->subMinute() : null,
    ]);
    $lesson = Lesson::create([
        'module_id' => $module->id,
        'title' => 'Test Lesson',
        'type' => 'text',
        'order_no' => 1,
        'published_at' => $lessonPublished ? now()->subMinute() : null,
    ]);

    return [$instructor, $section, $lesson];
}

function enrollStudentInLessonSection(User $student, CourseSection $section): void
{
    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);
}

// ─── Tests ────────────────────────────────────────────────────────────────────

test('enrolled student can view published lesson when module is also published', function (): void {
    [, $section, $lesson] = makeLessonAccessSetup(true, true);
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentInLessonSection($student, $section);

    $this->actingAs($student)
        ->get(route('lessons.show', $lesson))
        ->assertOk();
});

test('enrolled student gets 403 for unpublished lesson when module is published', function (): void {
    [, $section, $lesson] = makeLessonAccessSetup(false, true);
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentInLessonSection($student, $section);

    $this->actingAs($student)
        ->get(route('lessons.show', $lesson))
        ->assertForbidden();
});

test('enrolled student gets 403 when module is unpublished even if lesson is published', function (): void {
    [, $section, $lesson] = makeLessonAccessSetup(true, false);
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentInLessonSection($student, $section);

    $this->actingAs($student)
        ->get(route('lessons.show', $lesson))
        ->assertForbidden();
});

test('unenrolled student gets 403 on published lesson', function (): void {
    [, , $lesson] = makeLessonAccessSetup(true, true);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->get(route('lessons.show', $lesson))
        ->assertForbidden();
});

test('own instructor can view their draft lesson', function (): void {
    [$instructor, , $lesson] = makeLessonAccessSetup(false, false);

    $this->actingAs($instructor)
        ->get(route('lessons.show', $lesson))
        ->assertOk();
});

test('different instructor gets 403 on lessons.edit of another section lesson', function (): void {
    [, , $lesson] = makeLessonAccessSetup(false, false);
    $otherInstructor = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($otherInstructor)
        ->get(route('lessons.edit', $lesson))
        ->assertForbidden();
});
