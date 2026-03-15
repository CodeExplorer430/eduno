<?php

declare(strict_types=1);

use App\Domain\Course\Actions\UnenrollStudent;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Enums\UserRole;
use App\Models\User;

function makeEnrollment(): Enrollment
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);
    $course = Course::create([
        'code' => 'UE'.fake()->unique()->numberBetween(100, 999),
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

    return Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);
}

test('it sets enrollment status to withdrawn', function (): void {
    $enrollment = makeEnrollment();

    $action = new UnenrollStudent;
    $action->handle($enrollment);

    expect($enrollment->fresh()->status)->toBe('withdrawn');
});

test('it records the action in audit_logs', function (): void {
    $enrollment = makeEnrollment();

    $action = new UnenrollStudent;
    $action->handle($enrollment);

    $this->assertDatabaseHas('audit_logs', [
        'actor_id' => $enrollment->user_id,
        'action' => 'enrollment.deleted',
        'entity_id' => $enrollment->id,
    ]);
});
