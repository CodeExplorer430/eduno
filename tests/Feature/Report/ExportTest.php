<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Enums\UserRole;
use App\Models\User;

function makeExportSetup(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'EXP'.fake()->unique()->numberBetween(100, 999),
        'title' => 'Export Course',
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
        'title' => 'Export Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => now()->subMinute(),
    ]);

    return [$instructor, $section, $assignment];
}

test('instructor can export submissions as csv', function (): void {
    [$instructor, $section, $assignment] = makeExportSetup();

    $response = $this->actingAs($instructor)
        ->get(route('assignments.submissions.export', $assignment->id));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=utf-8');
    expect(str_contains($response->streamedContent(), 'student_name'))->toBeTrue();
});

test('student cannot export submissions', function (): void {
    [$instructor, $section, $assignment] = makeExportSetup();

    $student = User::factory()->create(['role' => UserRole::Student]);
    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);

    $response = $this->actingAs($student)
        ->get(route('assignments.submissions.export', $assignment->id));

    $response->assertForbidden();
});
