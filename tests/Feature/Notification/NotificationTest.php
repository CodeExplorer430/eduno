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

    (new PublishAnnouncement)->handle($announcement);

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

    (new PublishAnnouncement)->handle($announcement);

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

    (new SubmitAssignment)->handle($assignment, $student, [$file]);

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

    (new ReleaseGrade)->handle($grade);

    Notification::assertSentTo($student, GradeReleasedNotification::class);
});
