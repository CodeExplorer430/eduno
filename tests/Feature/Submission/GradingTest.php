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

function makeGradingSetup(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'GRD'.fake()->unique()->numberBetween(100, 999),
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
        'title' => 'Test Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => now()->subMinute(),
    ]);
    $student = User::factory()->create(['role' => UserRole::Student]);
    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);
    $submission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => SubmissionStatus::Submitted,
        'submitted_at' => now(),
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    return [$instructor, $student, $submission];
}

// ─── Grading ─────────────────────────────────────────────────────────────────

test('instructor can create a grade', function (): void {
    [$instructor, $student, $submission] = makeGradingSetup();

    $this->actingAs($instructor)
        ->post(route('submissions.grade.store', $submission), [
            'score' => 85,
            'feedback' => 'Good work!',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('grades', [
        'submission_id' => $submission->id,
        'score' => 85,
    ]);
});

test('instructor can update a grade', function (): void {
    [$instructor, $student, $submission] = makeGradingSetup();
    $grade = Grade::create([
        'submission_id' => $submission->id,
        'graded_by' => $instructor->id,
        'score' => 70,
        'feedback' => 'Initial feedback',
    ]);

    $this->actingAs($instructor)
        ->patch(route('grades.update', $grade), [
            'score' => 90,
            'feedback' => 'Revised feedback',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('grades', [
        'id' => $grade->id,
        'score' => 90,
    ]);
});

test('student cannot see grade before release', function (): void {
    [$instructor, $student, $submission] = makeGradingSetup();
    Grade::create([
        'submission_id' => $submission->id,
        'graded_by' => $instructor->id,
        'score' => 85,
        'feedback' => 'Good work!',
        'released_at' => null,
    ]);

    $response = $this->actingAs($student)
        ->get(route('submissions.show', $submission))
        ->assertOk();

    $props = $response->original->getData()['page']['props'];
    expect($props['submission']['grade']['released_at'])->toBeNull();
});

test('instructor can release grade', function (): void {
    [$instructor, $student, $submission] = makeGradingSetup();
    $grade = Grade::create([
        'submission_id' => $submission->id,
        'graded_by' => $instructor->id,
        'score' => 85,
        'feedback' => 'Good work!',
    ]);

    $this->actingAs($instructor)
        ->post(route('grades.release', $grade))
        ->assertRedirect();

    expect($grade->fresh()->released_at)->not->toBeNull();
});

test('student can see grade after release', function (): void {
    [$instructor, $student, $submission] = makeGradingSetup();
    $grade = Grade::create([
        'submission_id' => $submission->id,
        'graded_by' => $instructor->id,
        'score' => 88,
        'feedback' => 'Well done!',
        'released_at' => now()->subMinute(),
    ]);
    $submission->update(['status' => SubmissionStatus::Returned]);

    $response = $this->actingAs($student)
        ->get(route('submissions.show', $submission))
        ->assertOk();

    $props = $response->original->getData()['page']['props'];
    expect($props['submission']['grade']['score'])->toBe('88.00');
});

test('grade creation creates audit_log entry', function (): void {
    [$instructor, $student, $submission] = makeGradingSetup();

    $this->actingAs($instructor)
        ->post(route('submissions.grade.store', $submission), [
            'score' => 75,
            'feedback' => 'Audit test',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('audit_logs', [
        'actor_id' => $instructor->id,
        'action' => 'grade.created',
    ]);
});

test('grade release creates audit_log entry', function (): void {
    [$instructor, $student, $submission] = makeGradingSetup();
    $grade = Grade::create([
        'submission_id' => $submission->id,
        'graded_by' => $instructor->id,
        'score' => 80,
    ]);

    $this->actingAs($instructor)
        ->post(route('grades.release', $grade))
        ->assertRedirect();

    $this->assertDatabaseHas('audit_logs', [
        'actor_id' => $instructor->id,
        'action' => 'grade.released',
    ]);
});

test('student cannot grade a submission', function (): void {
    [$instructor, $student, $submission] = makeGradingSetup();

    $this->actingAs($student)
        ->post(route('submissions.grade.store', $submission), [
            'score' => 100,
        ])
        ->assertForbidden();
});

test('grade score cannot be negative', function (): void {
    [$instructor, , $submission] = makeGradingSetup();

    $this->actingAs($instructor)
        ->post(route('submissions.grade.store', $submission), [
            'score' => -1,
            'feedback' => 'Negative score attempt',
        ])
        ->assertSessionHasErrors(['score']);
});

test('grade score of zero is valid', function (): void {
    [$instructor, , $submission] = makeGradingSetup();

    $this->actingAs($instructor)
        ->post(route('submissions.grade.store', $submission), [
            'score' => 0,
            'feedback' => 'Zero score',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('grades', [
        'submission_id' => $submission->id,
        'score' => 0,
    ]);
});
