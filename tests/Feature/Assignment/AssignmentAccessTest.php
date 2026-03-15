<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Enums\UserRole;
use App\Models\User;

// ─── Helpers ─────────────────────────────────────────────────────────────────

function makeAssignmentAccessSetup(bool $published = true): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'AA'.fake()->unique()->numberBetween(100, 999),
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
    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Access Test Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => $published ? now()->subMinute() : null,
    ]);

    return [$instructor, $section, $assignment];
}

function enrollStudentInAssignmentSection(User $student, CourseSection $section): void
{
    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);
}

// ─── Tests ────────────────────────────────────────────────────────────────────

test('enrolled student can view published assignment', function (): void {
    [, $section, $assignment] = makeAssignmentAccessSetup(true);
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentInAssignmentSection($student, $section);

    $this->actingAs($student)
        ->get(route('assignments.show', $assignment))
        ->assertOk();
});

test('enrolled student gets 403 for draft assignment', function (): void {
    [, $section, $assignment] = makeAssignmentAccessSetup(false);
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentInAssignmentSection($student, $section);

    $this->actingAs($student)
        ->get(route('assignments.show', $assignment))
        ->assertForbidden();
});

test('unenrolled student gets 403 on published assignment', function (): void {
    [, , $assignment] = makeAssignmentAccessSetup(true);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->get(route('assignments.show', $assignment))
        ->assertForbidden();
});

test('own instructor can view their draft assignment', function (): void {
    [$instructor, , $assignment] = makeAssignmentAccessSetup(false);

    $this->actingAs($instructor)
        ->get(route('assignments.show', $assignment))
        ->assertOk();
});
