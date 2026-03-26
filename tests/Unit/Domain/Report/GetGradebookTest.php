<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Report\Actions\GetGradebook;
use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─── Fixtures ─────────────────────────────────────────────────────────────────

function makeGetGradebookFixture(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code'          => 'GB'.fake()->unique()->numberBetween(100, 999),
        'title'         => 'Gradebook Unit Test',
        'department'    => 'CCS',
        'term'          => '1st Semester',
        'academic_year' => '2025-2026',
        'status'        => 'published',
        'created_by'    => $instructor->id,
    ]);

    $section = CourseSection::create([
        'course_id'     => $course->id,
        'section_name'  => 'A',
        'instructor_id' => $instructor->id,
    ]);

    return [$instructor, $section];
}

function gbEnrollStudent(User $student, CourseSection $section): void
{
    Enrollment::create([
        'user_id'           => $student->id,
        'course_section_id' => $section->id,
        'status'            => 'active',
        'enrolled_at'       => now(),
    ]);
}

function gbPublishedAssignment(CourseSection $section, string $title = 'Assignment', float $max = 100): Assignment
{
    return Assignment::create([
        'course_section_id'  => $section->id,
        'title'              => $title,
        'max_score'          => $max,
        'allow_resubmission' => true,
        'published_at'       => now()->subMinute(),
        'due_at'             => now()->addDays(7),
    ]);
}

function gbGradeSubmission(User $instructor, User $student, Assignment $assignment, float $score, int $attempt = 1, bool $released = true): void
{
    $sub = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id'    => $student->id,
        'status'        => SubmissionStatus::Graded,
        'submitted_at'  => now(),
        'is_late'       => false,
        'attempt_no'    => $attempt,
    ]);

    Grade::create([
        'submission_id' => $sub->id,
        'graded_by'     => $instructor->id,
        'score'         => $score,
        'released_at'   => $released ? now() : null,
    ]);
}

// ─── Tests ────────────────────────────────────────────────────────────────────

test('students with no submissions have null grade cells', function (): void {
    [$instructor, $section] = makeGetGradebookFixture();
    $student    = User::factory()->create(['role' => UserRole::Student]);
    gbEnrollStudent($student, $section);
    $assignment = gbPublishedAssignment($section);

    $data = (new GetGradebook())($section);

    expect($data['students'])->toHaveCount(1);
    expect($data['students'][0]['grades'][$assignment->id])->toBeNull();
});

test('students with a graded submission show the correct score', function (): void {
    [$instructor, $section] = makeGetGradebookFixture();
    $student    = User::factory()->create(['role' => UserRole::Student]);
    gbEnrollStudent($student, $section);
    $assignment = gbPublishedAssignment($section);
    gbGradeSubmission($instructor, $student, $assignment, 87.5);

    $data = (new GetGradebook())($section);
    $cell = $data['students'][0]['grades'][$assignment->id];

    expect($cell)->not->toBeNull();
    expect($cell['score'])->toBe(87.5);
});

test('only the highest attempt graded submission is used when student resubmitted', function (): void {
    [$instructor, $section] = makeGetGradebookFixture();
    $student    = User::factory()->create(['role' => UserRole::Student]);
    gbEnrollStudent($student, $section);
    $assignment = gbPublishedAssignment($section);

    gbGradeSubmission($instructor, $student, $assignment, 60.0, attempt: 1);
    gbGradeSubmission($instructor, $student, $assignment, 88.0, attempt: 2);

    $data = (new GetGradebook())($section);
    $cell = $data['students'][0]['grades'][$assignment->id];

    expect($cell['score'])->toBe(88.0);
});

test('averages are computed from all graded submissions', function (): void {
    [$instructor, $section] = makeGetGradebookFixture();

    $studentA = User::factory()->create(['role' => UserRole::Student]);
    $studentB = User::factory()->create(['role' => UserRole::Student]);
    gbEnrollStudent($studentA, $section);
    gbEnrollStudent($studentB, $section);

    $assignment = gbPublishedAssignment($section);
    gbGradeSubmission($instructor, $studentA, $assignment, 80.0);
    gbGradeSubmission($instructor, $studentB, $assignment, 60.0);

    $data = (new GetGradebook())($section);

    expect($data['averages'][$assignment->id])->toBe(70.0);
});

test('unenrolled students are not included in the gradebook', function (): void {
    [$instructor, $section] = makeGetGradebookFixture();
    $enrolled   = User::factory()->create(['role' => UserRole::Student]);
    $unenrolled = User::factory()->create(['role' => UserRole::Student]);
    gbEnrollStudent($enrolled, $section);

    $data = (new GetGradebook())($section);

    $names = collect($data['students'])->pluck('name');
    expect($names)->toContain($enrolled->name);
    expect($names)->not->toContain($unenrolled->name);
});

test('draft assignments are excluded from the gradebook', function (): void {
    [$instructor, $section] = makeGetGradebookFixture();

    // Draft (no published_at)
    Assignment::create([
        'course_section_id'  => $section->id,
        'title'              => 'Draft Assignment',
        'max_score'          => 100,
        'allow_resubmission' => false,
    ]);

    $data = (new GetGradebook())($section);

    expect($data['assignments'])->toHaveCount(0);
});
