<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\User;

// ─── Helpers ─────────────────────────────────────────────────────────────────

function makeSectionForDashboard(User $instructor): CourseSection
{
    $course = Course::create([
        'code' => 'DSH'.fake()->unique()->numberBetween(100, 999),
        'title' => 'Dashboard Test Course',
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

function enrollStudentForDashboard(User $student, CourseSection $section): void
{
    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);
}

// ─── Auth ─────────────────────────────────────────────────────────────────────

test('unauthenticated user is redirected from /dashboard', function (): void {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

// ─── Student Dashboard ────────────────────────────────────────────────────────

test('student dashboard has required props', function (): void {
    $student = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($student)
        ->get(route('dashboard'))
        ->assertOk();

    $response->assertInertia(
        fn ($page) => $page
            ->component('Dashboard')
            ->has('courseSummary')
            ->has('upcoming')
            ->has('recentGrades')
    );
});

test('student upcoming assignments excludes assignments already submitted', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);
    $section = makeSectionForDashboard($instructor);
    enrollStudentForDashboard($student, $section);

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Already Submitted',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => now()->subMinute(),
        'due_at' => now()->addDays(3),
    ]);

    Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => SubmissionStatus::Submitted,
        'submitted_at' => now(),
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    $response = $this->actingAs($student)
        ->get(route('dashboard'))
        ->assertOk();

    $response->assertInertia(
        fn ($page) => $page
            ->component('Dashboard')
            ->where('upcoming', fn ($assignments) => collect($assignments)->where('id', $assignment->id)->isEmpty())
    );
});

test('student upcoming assignments excludes assignments due beyond 7 days', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);
    $section = makeSectionForDashboard($instructor);
    enrollStudentForDashboard($student, $section);

    Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Far Future Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => now()->subMinute(),
        'due_at' => now()->addDays(14),
    ]);

    $response = $this->actingAs($student)
        ->get(route('dashboard'))
        ->assertOk();

    $response->assertInertia(
        fn ($page) => $page
            ->component('Dashboard')
            ->where('upcoming', fn ($assignments) => collect($assignments)->where('title', 'Far Future Assignment')->isEmpty())
    );
});

// ─── Instructor Dashboard ─────────────────────────────────────────────────────

test('instructor dashboard has required props', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $response = $this->actingAs($instructor)
        ->get(route('dashboard'))
        ->assertOk();

    $response->assertInertia(
        fn ($page) => $page
            ->component('Dashboard')
            ->has('sections')
            ->has('pendingSubmissions')
    );
});

test('instructor pending submissions excludes graded submissions', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);
    $section = makeSectionForDashboard($instructor);
    enrollStudentForDashboard($student, $section);

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Graded Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => now()->subMinute(),
    ]);

    $submission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => SubmissionStatus::Submitted,
        'submitted_at' => now(),
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    Grade::create([
        'submission_id' => $submission->id,
        'graded_by' => $instructor->id,
        'score' => 90.00,
        'released_at' => null,
    ]);

    $response = $this->actingAs($instructor)
        ->get(route('dashboard'))
        ->assertOk();

    $response->assertInertia(
        fn ($page) => $page
            ->component('Dashboard')
            ->where('pendingSubmissions', fn ($subs) => collect($subs)->where('id', $submission->id)->isEmpty())
    );
});

// ─── Admin Dashboard ──────────────────────────────────────────────────────────

test('admin dashboard has report prop with all 6 keys', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk();

    $response->assertInertia(
        fn ($page) => $page
            ->component('Dashboard')
            ->has('report.total_courses')
            ->has('report.total_sections')
            ->has('report.total_students')
            ->has('report.total_submissions')
            ->has('report.late_submissions')
            ->has('report.graded_submissions')
    );
});
