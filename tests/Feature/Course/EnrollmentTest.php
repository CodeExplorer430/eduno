<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Enums\UserRole;
use App\Models\User;

function makeSectionForEnrollment(): CourseSection
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'SEC'.fake()->unique()->numberBetween(100, 999),
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

test('unauthenticated user cannot enroll', function (): void {
    $section = makeSectionForEnrollment();

    $this->post(route('sections.enroll', $section))
        ->assertRedirect(route('login'));
});

test('student can enroll in a section', function (): void {
    $student = User::factory()->create(['role' => UserRole::Student]);
    $section = makeSectionForEnrollment();

    $this->actingAs($student)
        ->post(route('sections.enroll', $section))
        ->assertRedirect();

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
    ]);
});

test('student cannot enroll in the same section twice', function (): void {
    $student = User::factory()->create(['role' => UserRole::Student]);
    $section = makeSectionForEnrollment();

    $this->actingAs($student)
        ->post(route('sections.enroll', $section));

    $this->actingAs($student)
        ->post(route('sections.enroll', $section))
        ->assertSessionHasErrors('enrollment');
});

test('student can unenroll from a section', function (): void {
    $student = User::factory()->create(['role' => UserRole::Student]);
    $section = makeSectionForEnrollment();

    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);

    $this->actingAs($student)
        ->delete(route('sections.unenroll', $section))
        ->assertRedirect();

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'withdrawn',
    ]);
});

test('unenroll fails when not enrolled', function (): void {
    $student = User::factory()->create(['role' => UserRole::Student]);
    $section = makeSectionForEnrollment();

    $this->actingAs($student)
        ->delete(route('sections.unenroll', $section))
        ->assertNotFound();
});
