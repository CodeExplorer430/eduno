<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Jobs\SendDeadlineReminder;
use App\Mail\DeadlineReminderMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

it('implements ShouldQueue', function (): void {
    expect(SendDeadlineReminder::class)->toImplement(ShouldQueue::class);
});

it('sends a DeadlineReminderMail to the student email', function (): void {
    Mail::fake();

    $instructor = User::factory()->create();
    $student    = User::factory()->create();

    $course = Course::create([
        'code'          => 'REM001',
        'title'         => 'Reminder Course',
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
        'title'             => 'Homework 1',
        'instructions'      => 'Do it.',
        'due_at'            => now()->addHours(2),
        'max_score'         => 100,
        'published_at'      => now()->subHour(),
    ]);

    (new SendDeadlineReminder($student, $assignment))->handle();

    Mail::assertSent(
        DeadlineReminderMail::class,
        fn (DeadlineReminderMail $mail) => $mail->hasTo($student->email)
            && $mail->assignment->id === $assignment->id
    );
});
