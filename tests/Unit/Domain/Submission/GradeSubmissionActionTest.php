<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Submission\Actions\GradeSubmission;
use App\Domain\Submission\Actions\ReleaseGrade;
use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\User;

function makeGradeActionSetup(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'UGS'.fake()->unique()->numberBetween(100, 999),
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
    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Unit Test Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => now()->subMinute(),
    ]);
    $student = User::factory()->create(['role' => UserRole::Student]);
    $submission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => SubmissionStatus::Submitted,
        'submitted_at' => now(),
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    return [$instructor, $submission];
}

test('it creates a grade record', function (): void {
    [$instructor, $submission] = makeGradeActionSetup();
    $action = new GradeSubmission;

    $grade = $action->handle($submission, $instructor, ['score' => 85, 'feedback' => 'Good']);

    expect($grade)->toBeInstanceOf(Grade::class)
        ->and($grade->score)->toBe('85.00')
        ->and($grade->submission_id)->toBe($submission->id);
});

test('it updates submission status to graded', function (): void {
    [$instructor, $submission] = makeGradeActionSetup();
    $action = new GradeSubmission;

    $action->handle($submission, $instructor, ['score' => 90]);

    expect($submission->fresh()->status)->toBe(SubmissionStatus::Graded);
});

test('it creates audit_log entry on grade creation', function (): void {
    [$instructor, $submission] = makeGradeActionSetup();
    $action = new GradeSubmission;

    $action->handle($submission, $instructor, ['score' => 75]);

    $this->assertDatabaseHas('audit_logs', [
        'actor_id' => $instructor->id,
        'action' => 'grade.created',
    ]);
});

test('it creates audit_log with grade.updated on second call', function (): void {
    [$instructor, $submission] = makeGradeActionSetup();
    $action = new GradeSubmission;

    $action->handle($submission, $instructor, ['score' => 70]);
    $action->handle($submission, $instructor, ['score' => 80]);

    $this->assertDatabaseHas('audit_logs', [
        'actor_id' => $instructor->id,
        'action' => 'grade.updated',
    ]);
});

test('ReleaseGrade sets released_at and status to returned', function (): void {
    [$instructor, $submission] = makeGradeActionSetup();
    $grade = Grade::create([
        'submission_id' => $submission->id,
        'graded_by' => $instructor->id,
        'score' => 88,
    ]);
    $action = new ReleaseGrade;

    $action->handle($grade);

    expect($grade->fresh()->released_at)->not->toBeNull()
        ->and($submission->fresh()->status)->toBe(SubmissionStatus::Returned);
});

test('ReleaseGrade creates audit_log entry', function (): void {
    [$instructor, $submission] = makeGradeActionSetup();
    $grade = Grade::create([
        'submission_id' => $submission->id,
        'graded_by' => $instructor->id,
        'score' => 90,
    ]);
    $action = new ReleaseGrade;

    $action->handle($grade);

    $this->assertDatabaseHas('audit_logs', [
        'actor_id' => $instructor->id,
        'action' => 'grade.released',
    ]);
});
