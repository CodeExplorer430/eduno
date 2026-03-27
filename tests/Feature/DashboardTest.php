<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Enums\UserRole;
use App\Models\User;

// ─── Helpers ─────────────────────────────────────────────────────────────────

function dashMakeSection(User $instructor): CourseSection
{
    $course = Course::create([
        'code'          => 'DSH' . fake()->unique()->numberBetween(100, 999),
        'title'         => 'Dashboard Test Course',
        'department'    => 'CS',
        'term'          => '1st',
        'academic_year' => '2025-2026',
        'status'        => 'published',
        'created_by'    => $instructor->id,
    ]);

    return CourseSection::create([
        'course_id'     => $course->id,
        'section_name'  => 'A',
        'instructor_id' => $instructor->id,
    ]);
}

function dashMakeAssignment(CourseSection $section): Assignment
{
    return Assignment::create([
        'course_section_id' => $section->id,
        'title'             => 'Test Assignment',
        'instructions'      => 'Do the thing.',
        'due_at'            => now()->addDays(3),
        'max_score'         => 100,
        'published_at'      => now(),
    ]);
}

function dashMakeSubmission(User $student, Assignment $assignment): Submission
{
    return Submission::create([
        'assignment_id' => $assignment->id,
        'student_id'    => $student->id,
        'status'        => 'submitted',
        'submitted_at'  => now(),
        'attempt_no'    => 1,
    ]);
}

// ─── Instructor Dashboard Prop Tests ─────────────────────────────────────────

test('instructor dashboard includes unreleased_grades_count', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student    = User::factory()->create(['role' => UserRole::Student]);
    $section    = dashMakeSection($instructor);

    Enrollment::create([
        'user_id'           => $student->id,
        'course_section_id' => $section->id,
        'status'            => 'active',
        'enrolled_at'       => now(),
    ]);

    $assignment = dashMakeAssignment($section);
    $submission = dashMakeSubmission($student, $assignment);

    // Grade assigned but NOT released
    Grade::create([
        'submission_id' => $submission->id,
        'graded_by'     => $instructor->id,
        'score'         => 85,
        'released_at'   => null,
    ]);

    $this->actingAs($instructor)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component('Dashboard')
                ->where('role', 'instructor')
                ->where('unreleased_grades_count', 1)
        );
});

test('instructor dashboard unreleased_grades_count excludes released grades', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student    = User::factory()->create(['role' => UserRole::Student]);
    $section    = dashMakeSection($instructor);

    Enrollment::create([
        'user_id'           => $student->id,
        'course_section_id' => $section->id,
        'status'            => 'active',
        'enrolled_at'       => now(),
    ]);

    $assignment = dashMakeAssignment($section);
    $submission = dashMakeSubmission($student, $assignment);

    // Grade assigned AND released
    Grade::create([
        'submission_id' => $submission->id,
        'graded_by'     => $instructor->id,
        'score'         => 90,
        'released_at'   => now(),
    ]);

    $this->actingAs($instructor)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page->where('unreleased_grades_count', 0)
        );
});

test('instructor dashboard includes flagged_count', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student    = User::factory()->create(['role' => UserRole::Student]);
    $section    = dashMakeSection($instructor);

    Enrollment::create([
        'user_id'           => $student->id,
        'course_section_id' => $section->id,
        'status'            => 'active',
        'enrolled_at'       => now(),
    ]);

    $assignment = dashMakeAssignment($section);
    $submission = dashMakeSubmission($student, $assignment);

    $submission->update(['flagged_for_review' => true]);

    $this->actingAs($instructor)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page->where('flagged_count', 1)
        );
});

test('instructor dashboard sections include course data', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    dashMakeSection($instructor);

    $this->actingAs($instructor)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component('Dashboard')
                ->where('role', 'instructor')
                ->has('sections', 1)
                ->has('sections.0.course')
                ->has('sections.0.course.code')
        );
});
