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

function makeFlagTestSubmission(User $instructor, User $student): Submission
{
    $course = Course::create([
        'code'          => 'FLAG'.random_int(100, 999),
        'title'         => 'Flag Test Course',
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
        'title'             => 'Test Assignment',
        'max_score'         => 100,
    ]);

    return Submission::create([
        'assignment_id' => $assignment->id,
        'student_id'    => $student->id,
        'status'        => 'submitted',
        'submitted_at'  => now(),
        'is_late'       => false,
        'attempt_no'    => 1,
    ]);
}

it('instructor can flag a submission', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student    = User::factory()->create(['role' => UserRole::Student]);
    $submission = makeFlagTestSubmission($instructor, $student);

    $this->actingAs($instructor)
        ->patch(route('instructor.submissions.flag', $submission))
        ->assertRedirect();

    expect($submission->fresh()->flagged_for_review)->toBeTrue();
});

it('instructor can unflag a submission', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student    = User::factory()->create(['role' => UserRole::Student]);
    $submission = makeFlagTestSubmission($instructor, $student);

    // Flag it first
    $this->actingAs($instructor)->patch(route('instructor.submissions.flag', $submission));
    // Then unflag
    $this->actingAs($instructor)->patch(route('instructor.submissions.flag', $submission));

    expect($submission->fresh()->flagged_for_review)->toBeFalse();
});

it('student cannot flag a submission', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student    = User::factory()->create(['role' => UserRole::Student]);
    $submission = makeFlagTestSubmission($instructor, $student);

    $this->actingAs($student)
        ->patch(route('instructor.submissions.flag', $submission))
        ->assertForbidden();
});

it('other instructor cannot flag a submission', function (): void {
    $owner       = User::factory()->create(['role' => UserRole::Instructor]);
    $other       = User::factory()->create(['role' => UserRole::Instructor]);
    $student     = User::factory()->create(['role' => UserRole::Student]);
    $submission  = makeFlagTestSubmission($owner, $student);

    $this->actingAs($other)
        ->patch(route('instructor.submissions.flag', $submission))
        ->assertForbidden();
});
