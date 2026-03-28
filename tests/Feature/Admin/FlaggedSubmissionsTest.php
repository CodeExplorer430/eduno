<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Submission\Models\Submission;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeSubmissionForFlaggedTest(User $instructor, User $student, bool $flagged): Submission
{
    $course = Course::create([
        'code'          => 'ADM'.random_int(100, 999),
        'title'         => 'Admin Flag Test Course',
        'department'    => 'CS',
        'term'          => '1st',
        'academic_year' => '2025-2026',
        'status'        => 'published',
        'created_by'    => $instructor->id,
    ]);

    $section = CourseSection::create([
        'course_id'     => $course->id,
        'section_name'  => 'A',
        'instructor_id' => $instructor->id,
    ]);

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title'             => 'Assignment',
        'max_score'         => 100,
    ]);

    return Submission::create([
        'assignment_id'      => $assignment->id,
        'student_id'         => $student->id,
        'status'             => 'submitted',
        'submitted_at'       => now(),
        'is_late'            => false,
        'attempt_no'         => 1,
        'flagged_for_review' => $flagged,
    ]);
}

it('admin can view flagged submissions list', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('admin.flagged-submissions.index'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page->component('Admin/FlaggedSubmissions/Index')->has('submissions')
    );
});

it('only flagged submissions appear in the list', function (): void {
    $admin      = User::factory()->create(['role' => UserRole::Admin]);
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student    = User::factory()->create(['role' => UserRole::Student]);

    $flagged    = makeSubmissionForFlaggedTest($instructor, $student, flagged: true);
    $notFlagged = makeSubmissionForFlaggedTest($instructor, $student, flagged: false);

    $response = $this->actingAs($admin)->get(route('admin.flagged-submissions.index'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
            ->has('submissions.data', 1)
            ->where('submissions.data.0.id', $flagged->id)
    );
});

it('instructor cannot access admin flagged submissions', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($instructor)
        ->get(route('admin.flagged-submissions.index'))
        ->assertForbidden();
});

it('student cannot access admin flagged submissions', function (): void {
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->get(route('admin.flagged-submissions.index'))
        ->assertForbidden();
});
