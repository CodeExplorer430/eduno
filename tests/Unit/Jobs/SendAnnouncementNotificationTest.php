<?php

declare(strict_types=1);

use App\Domain\Accessibility\Models\UserPreference;
use App\Enums\UserRole;
use App\Jobs\SendAnnouncementNotification;
use App\Mail\AnnouncementPublishedMail;
use App\Models\User;
use Database\Factories\AnnouncementFactory;
use Database\Factories\CourseSectionFactory;
use Database\Factories\EnrollmentFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

it('implements ShouldQueue', function (): void {
    $interfaces = class_implements(SendAnnouncementNotification::class);
    expect($interfaces)->toContain(ShouldQueue::class);
});

it('does not send mail to students who have opted out of email notifications', function (): void {
    Mail::fake();

    $section = CourseSectionFactory::new()->create();
    $announcement = AnnouncementFactory::new()->published()->create(['course_section_id' => $section->id]);

    $student = User::factory()->create(['role' => UserRole::Student]);
    UserPreference::create(['user_id' => $student->id, 'email_notifications' => false]);
    EnrollmentFactory::new()->create(['user_id' => $student->id, 'course_section_id' => $section->id]);

    (new SendAnnouncementNotification($announcement))->handle();

    Mail::assertNothingSent();
});

it('sends mail to students who have email notifications enabled', function (): void {
    Mail::fake();

    $section = CourseSectionFactory::new()->create();
    $announcement = AnnouncementFactory::new()->published()->create(['course_section_id' => $section->id]);

    $student = User::factory()->create(['role' => UserRole::Student]);
    UserPreference::create(['user_id' => $student->id, 'email_notifications' => true]);
    EnrollmentFactory::new()->create(['user_id' => $student->id, 'course_section_id' => $section->id]);

    (new SendAnnouncementNotification($announcement))->handle();

    Mail::assertQueued(AnnouncementPublishedMail::class);
});

it('sends mail to students with no preference record', function (): void {
    Mail::fake();

    $section = CourseSectionFactory::new()->create();
    $announcement = AnnouncementFactory::new()->published()->create(['course_section_id' => $section->id]);

    $student = User::factory()->create(['role' => UserRole::Student]);
    // No UserPreference created — preferences is null, should still receive mail
    EnrollmentFactory::new()->create(['user_id' => $student->id, 'course_section_id' => $section->id]);

    (new SendAnnouncementNotification($announcement))->handle();

    Mail::assertQueued(AnnouncementPublishedMail::class);
});
