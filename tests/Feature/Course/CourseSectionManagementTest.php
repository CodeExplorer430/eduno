<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;

function makeCourseForSection(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'CS'.fake()->unique()->numberBetween(100, 999),
        'title' => 'Test Course',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    return [$instructor, $course];
}

// ─── Auth Gate ────────────────────────────────────────────────────────────────

test('unauthenticated user is redirected from section create', function (): void {
    [, $course] = makeCourseForSection();

    $this->get(route('courses.sections.create', $course))
        ->assertRedirect(route('login'));
});

// ─── Create ───────────────────────────────────────────────────────────────────

test('instructor can create a section under their course', function (): void {
    [$instructor, $course] = makeCourseForSection();

    $this->actingAs($instructor)
        ->post(route('courses.sections.store', $course), [
            'section_name' => 'Section A',
            'course_id' => $course->id,
            'instructor_id' => $instructor->id,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('course_sections', [
        'course_id' => $course->id,
        'section_name' => 'Section A',
        'instructor_id' => $instructor->id,
    ]);
});

test('instructor cannot create a section under another instructor\'s course', function (): void {
    [$originalInstructor, $course] = makeCourseForSection();
    $otherInstructor = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($otherInstructor)
        ->post(route('courses.sections.store', $course), [
            'section_name' => 'Section B',
            'course_id' => $course->id,
            'instructor_id' => $otherInstructor->id,
        ])
        ->assertForbidden();
});

test('student cannot create a section', function (): void {
    [$instructor, $course] = makeCourseForSection();
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->post(route('courses.sections.store', $course), [
            'section_name' => 'Section C',
            'course_id' => $course->id,
            'instructor_id' => $instructor->id,
        ])
        ->assertForbidden();
});

test('section creation fails with missing section_name', function (): void {
    [$instructor, $course] = makeCourseForSection();

    $this->actingAs($instructor)
        ->post(route('courses.sections.store', $course), [
            'course_id' => $course->id,
            'instructor_id' => $instructor->id,
        ])
        ->assertSessionHasErrors('section_name');
});

// ─── Update ───────────────────────────────────────────────────────────────────

test('instructor can update their section', function (): void {
    [$instructor, $course] = makeCourseForSection();
    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'Old Name',
        'instructor_id' => $instructor->id,
    ]);

    $this->actingAs($instructor)
        ->put(route('sections.update', $section), [
            'section_name' => 'New Name',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('course_sections', [
        'id' => $section->id,
        'section_name' => 'New Name',
    ]);
});

// ─── Delete ───────────────────────────────────────────────────────────────────

test('instructor can delete their section', function (): void {
    [$instructor, $course] = makeCourseForSection();
    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'To Delete',
        'instructor_id' => $instructor->id,
    ]);

    $this->actingAs($instructor)
        ->delete(route('sections.destroy', $section))
        ->assertRedirect();

    $this->assertDatabaseMissing('course_sections', ['id' => $section->id]);
});
