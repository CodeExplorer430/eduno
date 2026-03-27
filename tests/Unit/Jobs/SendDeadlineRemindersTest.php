<?php

declare(strict_types=1);

use App\Domain\Accessibility\Models\UserPreference;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Submission\Models\Submission;
use App\Enums\UserRole;
use App\Jobs\SendDeadlineReminder;
use App\Jobs\SendDeadlineReminders;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

// ─── Helpers ─────────────────────────────────────────────────────────────────

function reminderMakeSection(User $instructor): CourseSection
{
    $course = Course::create([
        'code'          => 'RMD' . fake()->unique()->numberBetween(100, 999),
        'title'         => 'Reminder Test Course',
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

function reminderMakeAssignment(CourseSection $section, int $dueInHours = 12, bool $published = true): Assignment
{
    return Assignment::create([
        'course_section_id' => $section->id,
        'title'             => 'Test Assignment',
        'instructions'      => 'Do it.',
        'due_at'            => now()->addHours($dueInHours),
        'max_score'         => 100,
        'published_at'      => $published ? now()->subHour() : null,
    ]);
}

function reminderEnroll(User $student, CourseSection $section, string $status = 'active'): Enrollment
{
    return Enrollment::create([
        'user_id'           => $student->id,
        'course_section_id' => $section->id,
        'status'            => $status,
        'enrolled_at'       => now(),
    ]);
}

// ─── Tests ────────────────────────────────────────────────────────────────────

test('dispatches a SendDeadlineReminder job for each unsubmitted enrolled student', function (): void {
    Queue::fake();

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student1   = User::factory()->create(['role' => UserRole::Student]);
    $student2   = User::factory()->create(['role' => UserRole::Student]);
    $section    = reminderMakeSection($instructor);
    $assignment = reminderMakeAssignment($section);

    reminderEnroll($student1, $section);
    reminderEnroll($student2, $section);

    (new SendDeadlineReminders())->handle();

    Queue::assertPushed(SendDeadlineReminder::class, 2);
});

test('does not dispatch for students who already submitted', function (): void {
    Queue::fake();

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student    = User::factory()->create(['role' => UserRole::Student]);
    $section    = reminderMakeSection($instructor);
    $assignment = reminderMakeAssignment($section);

    reminderEnroll($student, $section);

    Submission::create([
        'assignment_id' => $assignment->id,
        'student_id'    => $student->id,
        'status'        => 'submitted',
        'submitted_at'  => now(),
        'attempt_no'    => 1,
    ]);

    (new SendDeadlineReminders())->handle();

    Queue::assertNotPushed(SendDeadlineReminder::class);
});

test('does not dispatch for students with email_notifications disabled', function (): void {
    Queue::fake();

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student    = User::factory()->create(['role' => UserRole::Student]);
    $section    = reminderMakeSection($instructor);

    reminderMakeAssignment($section);
    reminderEnroll($student, $section);

    UserPreference::create([
        'user_id'             => $student->id,
        'email_notifications' => false,
    ]);

    (new SendDeadlineReminders())->handle();

    Queue::assertNotPushed(SendDeadlineReminder::class);
});

test('does not dispatch for assignments due beyond 24 hours', function (): void {
    Queue::fake();

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student    = User::factory()->create(['role' => UserRole::Student]);
    $section    = reminderMakeSection($instructor);

    reminderMakeAssignment($section, dueInHours: 25); // outside the 24-hour window
    reminderEnroll($student, $section);

    (new SendDeadlineReminders())->handle();

    Queue::assertNotPushed(SendDeadlineReminder::class);
});

test('does not dispatch for unpublished assignments', function (): void {
    Queue::fake();

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student    = User::factory()->create(['role' => UserRole::Student]);
    $section    = reminderMakeSection($instructor);

    reminderMakeAssignment($section, published: false);
    reminderEnroll($student, $section);

    (new SendDeadlineReminders())->handle();

    Queue::assertNotPushed(SendDeadlineReminder::class);
});

test('does not dispatch for inactive enrollments', function (): void {
    Queue::fake();

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student    = User::factory()->create(['role' => UserRole::Student]);
    $section    = reminderMakeSection($instructor);

    reminderMakeAssignment($section);
    reminderEnroll($student, $section, status: 'withdrawn');

    (new SendDeadlineReminders())->handle();

    Queue::assertNotPushed(SendDeadlineReminder::class);
});
