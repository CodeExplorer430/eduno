<?php

declare(strict_types=1);

use App\Domain\Announcement\Actions\PublishAnnouncement;
use App\Domain\Announcement\Models\Announcement;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Submission\Actions\ReleaseGrade;
use App\Domain\Submission\Actions\SubmitAssignment;
use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\AnnouncementPublishedNotification;
use App\Notifications\DeadlineReminderNotification;
use App\Notifications\GradeReleasedNotification;
use App\Notifications\NewSubmissionNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

function makeNotificationSetup(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'NOTIF'.fake()->unique()->numberBetween(100, 999),
        'title' => 'Notification Course',
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
    $student = User::factory()->create(['role' => UserRole::Student]);
    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);

    return [$instructor, $section, $student];
}

test('publishing announcement sends notification to enrolled students', function (): void {
    Notification::fake();

    [$instructor, $section, $student] = makeNotificationSetup();

    $announcement = Announcement::create([
        'course_section_id' => $section->id,
        'title' => 'Test Announcement',
        'body' => 'Hello class!',
        'created_by' => $instructor->id,
        'published_at' => null,
    ]);

    (new PublishAnnouncement())->handle($announcement);

    Notification::assertSentTo($student, AnnouncementPublishedNotification::class);
});

test('unpublishing announcement does not send notification', function (): void {
    Notification::fake();

    [$instructor, $section] = makeNotificationSetup();

    $announcement = Announcement::create([
        'course_section_id' => $section->id,
        'title' => 'Test Announcement',
        'body' => 'Hello class!',
        'created_by' => $instructor->id,
        'published_at' => now(),
    ]);

    (new PublishAnnouncement())->handle($announcement);

    Notification::assertNothingSent();
});

test('student submitting assignment sends notification to instructor', function (): void {
    Notification::fake();
    Storage::fake('private');

    [$instructor, $section, $student] = makeNotificationSetup();

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Test Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => now()->subMinute(),
    ]);

    $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

    (new SubmitAssignment())->handle($assignment, $student, [$file]);

    Notification::assertSentTo($instructor, NewSubmissionNotification::class);
});

test('releasing grade sends notification to student', function (): void {
    Notification::fake();

    [$instructor, $section, $student] = makeNotificationSetup();

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Test Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => now()->subMinute(),
    ]);

    $submission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => SubmissionStatus::Submitted,
        'submitted_at' => now(),
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    $grade = Grade::create([
        'submission_id' => $submission->id,
        'graded_by' => $instructor->id,
        'score' => 90,
        'feedback' => 'Great work!',
        'released_at' => null,
    ]);

    (new ReleaseGrade())->handle($grade);

    Notification::assertSentTo($student, GradeReleasedNotification::class);
});

test('deadline reminder is sent to enrolled student who has not submitted', function (): void {
    Notification::fake();
    [$instructor, $section, $student] = makeNotificationSetup();

    Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Due Soon',
        'max_score' => 100,
        'allow_resubmission' => false,
        'due_at' => now()->addHours(12),
        'published_at' => now()->subMinute(),
    ]);

    $this->artisan('schedule:deadline-reminders')->assertSuccessful();

    Notification::assertSentTo($student, DeadlineReminderNotification::class);
});

test('deadline reminder is not sent to student who has already submitted', function (): void {
    Notification::fake();
    [$instructor, $section, $student] = makeNotificationSetup();

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Due Soon Submitted',
        'max_score' => 100,
        'allow_resubmission' => false,
        'due_at' => now()->addHours(12),
        'published_at' => now()->subMinute(),
    ]);

    Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => SubmissionStatus::Submitted,
        'submitted_at' => now(),
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    $this->artisan('schedule:deadline-reminders')->assertSuccessful();

    Notification::assertNotSentTo($student, DeadlineReminderNotification::class);
});

test('deadline reminder is not sent for unpublished assignment', function (): void {
    Notification::fake();
    [$instructor, $section, $student] = makeNotificationSetup();

    Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Draft Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'due_at' => now()->addHours(12),
        'published_at' => null,
    ]);

    $this->artisan('schedule:deadline-reminders')->assertSuccessful();

    Notification::assertNotSentTo($student, DeadlineReminderNotification::class);
});

test('deadline reminder is not sent for assignment due beyond 24 hours', function (): void {
    Notification::fake();
    [$instructor, $section, $student] = makeNotificationSetup();

    Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Far Future Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'due_at' => now()->addHours(48),
        'published_at' => now()->subMinute(),
    ]);

    $this->artisan('schedule:deadline-reminders')->assertSuccessful();

    Notification::assertNotSentTo($student, DeadlineReminderNotification::class);
});

test('deadline reminder is not sent to withdrawn student', function (): void {
    Notification::fake();
    [$instructor, $section, $student] = makeNotificationSetup();

    // Withdraw the student from the section.
    Enrollment::where('user_id', $student->id)
        ->where('course_section_id', $section->id)
        ->update(['status' => 'withdrawn']);

    Assignment::create([
        'course_section_id' => $section->id,
        'title'             => 'Due Soon',
        'max_score'         => 100,
        'allow_resubmission' => false,
        'due_at'            => now()->addHours(12),
        'published_at'      => now()->subMinute(),
    ]);

    $this->artisan('schedule:deadline-reminders')->assertSuccessful();

    Notification::assertNotSentTo($student, DeadlineReminderNotification::class);
});

// ---------------------------------------------------------------------------
// Notification Center (in-app database channel) feature tests
// ---------------------------------------------------------------------------

test('authenticated user can view their notification list', function (): void {
    $user = User::factory()->create();

    \Illuminate\Support\Facades\DB::table('notifications')->insert([
        'id'              => (string) \Illuminate\Support\Str::uuid(),
        'type'            => 'App\\Notifications\\GradeReleasedNotification',
        'notifiable_type' => 'App\\Models\\User',
        'notifiable_id'   => $user->id,
        'data'            => json_encode(['message' => 'Test', 'url' => '/', 'type' => 'grade_released']),
        'read_at'         => null,
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);

    $response = $this->actingAs($user)->get(route('notifications.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('Notifications/Index'));
});

test('unauthenticated user is redirected from notifications index', function (): void {
    $this->get(route('notifications.index'))->assertRedirect(route('login'));
});

test('show redirects to notification url and marks it read', function (): void {
    $user = User::factory()->create();
    $notifId = (string) \Illuminate\Support\Str::uuid();

    \Illuminate\Support\Facades\DB::table('notifications')->insert([
        'id'              => $notifId,
        'type'            => 'App\\Notifications\\GradeReleasedNotification',
        'notifiable_type' => 'App\\Models\\User',
        'notifiable_id'   => $user->id,
        'data'            => json_encode(['message' => 'Grade released', 'url' => 'https://example.com', 'type' => 'grade_released']),
        'read_at'         => null,
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);

    $this->actingAs($user)->get(route('notifications.show', $notifId));

    expect(
        \Illuminate\Support\Facades\DB::table('notifications')
            ->where('id', $notifId)
            ->whereNotNull('read_at')
            ->exists()
    )->toBeTrue();
});

test('user cannot view another user notification', function (): void {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $notifId = (string) \Illuminate\Support\Str::uuid();

    \Illuminate\Support\Facades\DB::table('notifications')->insert([
        'id'              => $notifId,
        'type'            => 'App\\Notifications\\GradeReleasedNotification',
        'notifiable_type' => 'App\\Models\\User',
        'notifiable_id'   => $owner->id,
        'data'            => json_encode(['message' => 'Secret', 'url' => '/', 'type' => 'grade_released']),
        'read_at'         => null,
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);

    $this->actingAs($other)
        ->get(route('notifications.show', $notifId))
        ->assertNotFound();
});

test('markAllAsRead clears unread count', function (): void {
    $user = User::factory()->create();

    foreach (range(1, 3) as $i) {
        \Illuminate\Support\Facades\DB::table('notifications')->insert([
            'id'              => (string) \Illuminate\Support\Str::uuid(),
            'type'            => 'App\\Notifications\\GradeReleasedNotification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id'   => $user->id,
            'data'            => json_encode(['message' => "Notification {$i}", 'url' => '/', 'type' => 'grade_released']),
            'read_at'         => null,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }

    expect($user->fresh()->unreadNotifications()->count())->toBe(3);

    $this->actingAs($user)
        ->post(route('notifications.read-all'))
        ->assertRedirect();

    expect($user->fresh()->unreadNotifications()->count())->toBe(0);
});

test('notification list is paginated', function (): void {
    $user = User::factory()->create();

    foreach (range(1, 20) as $i) {
        \Illuminate\Support\Facades\DB::table('notifications')->insert([
            'id'              => (string) \Illuminate\Support\Str::uuid(),
            'type'            => 'App\\Notifications\\GradeReleasedNotification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id'   => $user->id,
            'data'            => json_encode(['message' => "Notification {$i}", 'url' => '/', 'type' => 'grade_released']),
            'read_at'         => null,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
            ->component('Notifications/Index')
            ->has('notifications.data', 15)
        );
});
