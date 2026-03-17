<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Submission\Models\Submission;
use App\Enums\UserRole;
use App\Jobs\SendDeadlineReminder;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

it('dispatches SendDeadlineReminder for each student without a submission', function () {
    Queue::fake();

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'RMD101',
        'title' => 'Reminder Dispatch Test',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'A',
        'instructor_id' => $instructor->id,
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);

    Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Due Tomorrow Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'due_at' => now()->addHours(12),
        'published_at' => now(),
    ]);

    $this->artisan('reminders:deadlines');

    Queue::assertPushed(SendDeadlineReminder::class, 1);
});

it('does not dispatch reminder for students who already submitted', function () {
    Queue::fake();

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'RMD102',
        'title' => 'Reminder Skip Test',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'A',
        'instructor_id' => $instructor->id,
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Already Submitted Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'due_at' => now()->addHours(12),
        'published_at' => now(),
    ]);

    Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => 'submitted',
        'is_late' => false,
        'attempt_no' => 1,
        'submitted_at' => now(),
    ]);

    $this->artisan('reminders:deadlines');

    Queue::assertNotPushed(SendDeadlineReminder::class);
});
