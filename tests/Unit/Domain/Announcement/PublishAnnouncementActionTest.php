<?php

declare(strict_types=1);

use App\Domain\Accessibility\Models\UserPreference;
use App\Domain\Announcement\Actions\PublishAnnouncement;
use App\Domain\Announcement\Models\Announcement;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\AnnouncementPublishedNotification;
use Illuminate\Support\Facades\Notification;

function makePublishTestSection(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'UPB'.fake()->unique()->numberBetween(100, 999),
        'title' => 'Publish Test Course',
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

    return [$instructor, $section];
}

test('publishing an unpublished announcement sets published_at', function (): void {
    [$instructor, $section] = makePublishTestSection();

    $announcement = Announcement::create([
        'course_section_id' => $section->id,
        'title' => 'Draft',
        'body' => 'Body.',
        'created_by' => $instructor->id,
        'published_at' => null,
    ]);

    (new PublishAnnouncement())->handle($announcement);

    expect($announcement->fresh()->published_at)->not->toBeNull();
});

test('toggling a published announcement sets published_at to null', function (): void {
    [$instructor, $section] = makePublishTestSection();

    $announcement = Announcement::create([
        'course_section_id' => $section->id,
        'title' => 'Already Published',
        'body' => 'Body.',
        'created_by' => $instructor->id,
        'published_at' => now()->subMinute(),
    ]);

    (new PublishAnnouncement())->handle($announcement);

    expect($announcement->fresh()->published_at)->toBeNull();
});

test('handle returns the announcement instance', function (): void {
    [$instructor, $section] = makePublishTestSection();

    $announcement = Announcement::create([
        'course_section_id' => $section->id,
        'title' => 'Return Check',
        'body' => 'Body.',
        'created_by' => $instructor->id,
        'published_at' => null,
    ]);

    $result = (new PublishAnnouncement())->handle($announcement);

    expect($result)->toBeInstanceOf(Announcement::class)
        ->and($result->id)->toBe($announcement->id);
});

test('publishing an announcement whose section was deleted sets published_at without crashing', function (): void {
    Notification::fake();
    [$instructor, $section] = makePublishTestSection();

    $announcement = Announcement::create([
        'course_section_id' => $section->id,
        'title' => 'Orphaned Announcement',
        'body' => 'Body.',
        'created_by' => $instructor->id,
        'published_at' => null,
    ]);

    // Override the section relation to always return null, simulating an orphaned
    // announcement whose section was deleted — without touching real DB rows.
    Announcement::resolveRelationUsing(
        'section',
        fn ($m) => $m->belongsTo(CourseSection::class, 'course_section_id')->whereNull('id')
    );

    (new PublishAnnouncement())->handle($announcement);

    expect($announcement->fresh()->published_at)->not->toBeNull();
    Notification::assertNothingSent();

    // Cleanup: remove the static resolver so subsequent tests use the real section() method.
    $reflection = new ReflectionClass(Announcement::class);
    $property = $reflection->getProperty('relationResolvers');
    $property->setAccessible(true);
    $resolvers = $property->getValue(null);
    unset($resolvers[Announcement::class]['section']);
    $property->setValue(null, $resolvers);
});

// ─── Notification & Email Tests ───────────────────────────────────────────────

test('notification is sent to active enrolled students on first publish', function (): void {
    Notification::fake();
    [$instructor, $section] = makePublishTestSection();

    $active1   = User::factory()->create(['role' => UserRole::Student]);
    $active2   = User::factory()->create(['role' => UserRole::Student]);
    $inactive  = User::factory()->create(['role' => UserRole::Student]);

    Enrollment::create(['user_id' => $active1->id, 'course_section_id' => $section->id, 'status' => 'active', 'enrolled_at' => now()]);
    Enrollment::create(['user_id' => $active2->id, 'course_section_id' => $section->id, 'status' => 'active', 'enrolled_at' => now()]);
    Enrollment::create(['user_id' => $inactive->id, 'course_section_id' => $section->id, 'status' => 'dropped', 'enrolled_at' => now()]);

    $announcement = Announcement::create([
        'course_section_id' => $section->id,
        'title'             => 'Notification Test',
        'body'              => 'Body.',
        'created_by'        => $instructor->id,
        'published_at'      => null,
    ]);

    (new PublishAnnouncement())->handle($announcement);

    Notification::assertSentTo($active1, AnnouncementPublishedNotification::class);
    Notification::assertSentTo($active2, AnnouncementPublishedNotification::class);
    Notification::assertNotSentTo($inactive, AnnouncementPublishedNotification::class);
});

test('no notification is sent when announcement is unpublished', function (): void {
    Notification::fake();
    [$instructor, $section] = makePublishTestSection();

    $student = User::factory()->create(['role' => UserRole::Student]);
    Enrollment::create(['user_id' => $student->id, 'course_section_id' => $section->id, 'status' => 'active', 'enrolled_at' => now()]);

    $announcement = Announcement::create([
        'course_section_id' => $section->id,
        'title'             => 'Published First',
        'body'              => 'Body.',
        'created_by'        => $instructor->id,
        'published_at'      => now()->subMinute(),
    ]);

    // Second call toggles off — should send no notification.
    (new PublishAnnouncement())->handle($announcement);

    Notification::assertNothingSent();
});

test('notification uses mail channel when user has no preferences row', function (): void {
    Notification::fake();
    [$instructor, $section] = makePublishTestSection();

    $student = User::factory()->create(['role' => UserRole::Student]);
    Enrollment::create(['user_id' => $student->id, 'course_section_id' => $section->id, 'status' => 'active', 'enrolled_at' => now()]);
    // No UserPreference row — mail should be included by default.

    $announcement = Announcement::create([
        'course_section_id' => $section->id,
        'title'             => 'Default Prefs',
        'body'              => 'Body.',
        'created_by'        => $instructor->id,
        'published_at'      => null,
    ]);

    (new PublishAnnouncement())->handle($announcement);

    Notification::assertSentTo(
        $student,
        AnnouncementPublishedNotification::class,
        fn ($notification, $channels) => in_array('mail', $channels, true)
    );
});

test('notification skips mail channel when user has email_notifications disabled', function (): void {
    Notification::fake();
    [$instructor, $section] = makePublishTestSection();

    $student = User::factory()->create(['role' => UserRole::Student]);
    Enrollment::create(['user_id' => $student->id, 'course_section_id' => $section->id, 'status' => 'active', 'enrolled_at' => now()]);

    UserPreference::create([
        'user_id'             => $student->id,
        'email_notifications' => false,
    ]);

    $announcement = Announcement::create([
        'course_section_id' => $section->id,
        'title'             => 'Opt-Out Test',
        'body'              => 'Body.',
        'created_by'        => $instructor->id,
        'published_at'      => null,
    ]);

    (new PublishAnnouncement())->handle($announcement);

    Notification::assertSentTo(
        $student,
        AnnouncementPublishedNotification::class,
        fn ($notification, $channels) => ! in_array('mail', $channels, true)
                                      && in_array('database', $channels, true)
    );
});
