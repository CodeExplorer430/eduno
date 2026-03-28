<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Submission\Actions\FlagSubmission;
use App\Domain\Submission\Models\Submission;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeSubmissionForFlagAction(bool $flagged): Submission
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code'          => 'FLAG' . random_int(100, 999),
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
        'assignment_id'      => $assignment->id,
        'student_id'         => $student->id,
        'status'             => 'submitted',
        'submitted_at'       => now(),
        'is_late'            => false,
        'attempt_no'         => 1,
        'flagged_for_review' => $flagged,
    ]);
}

test('flag submission toggles flagged_for_review to true', function (): void {
    $submission = makeSubmissionForFlagAction(false);

    (new FlagSubmission())->handle($submission);

    expect($submission->fresh()->flagged_for_review)->toBeTrue();
});

test('flag submission toggles flagged_for_review back to false', function (): void {
    $submission = makeSubmissionForFlagAction(true);

    (new FlagSubmission())->handle($submission);

    expect($submission->fresh()->flagged_for_review)->toBeFalse();
});
