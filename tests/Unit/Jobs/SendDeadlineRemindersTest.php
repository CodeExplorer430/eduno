<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Jobs\SendDeadlineReminder;
use App\Jobs\SendDeadlineReminders;
use App\Models\Setting;
use App\Models\User;
use Database\Factories\AssignmentFactory;
use Database\Factories\EnrollmentFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Queue;

it('implements ShouldQueue', function (): void {
    $interfaces = class_implements(SendDeadlineReminders::class);
    expect($interfaces)->toContain(ShouldQueue::class);
});

it('reads deadline_reminder_hours from settings instead of using a hardcoded value', function (): void {
    Queue::fake();

    Setting::set('deadline_reminder_hours', '48', 'integer', 'notifications');

    $student = User::factory()->create(['role' => UserRole::Student]);

    // Assignment due in 36 hours — inside a 48h window but outside a 24h window
    $assignment = AssignmentFactory::new()->published()->create([
        'due_at' => now()->addHours(36),
    ]);
    EnrollmentFactory::new()->create([
        'user_id' => $student->id,
        'course_section_id' => $assignment->course_section_id,
    ]);

    (new SendDeadlineReminders())->handle();

    Queue::assertPushed(
        SendDeadlineReminder::class,
        fn ($job) => $job->assignment->id === $assignment->id
        && $job->student->id === $student->id
    );
});

it('does not dispatch reminders for assignments outside the configured window', function (): void {
    Queue::fake();

    Setting::set('deadline_reminder_hours', '24', 'integer', 'notifications');

    $student = User::factory()->create(['role' => UserRole::Student]);

    // Assignment due in 36 hours — outside the 24h window
    $assignment = AssignmentFactory::new()->published()->create([
        'due_at' => now()->addHours(36),
    ]);
    EnrollmentFactory::new()->create([
        'user_id' => $student->id,
        'course_section_id' => $assignment->course_section_id,
    ]);

    (new SendDeadlineReminders())->handle();

    Queue::assertNotPushed(SendDeadlineReminder::class);
});
