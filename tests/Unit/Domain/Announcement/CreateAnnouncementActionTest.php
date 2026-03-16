<?php

declare(strict_types=1);

use App\Domain\Announcement\Actions\CreateAnnouncement;
use App\Domain\Announcement\Models\Announcement;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Jobs\SendAnnouncementNotification;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

it('creates an Announcement record', function () {
    Queue::fake();

    $action = new CreateAnnouncement;

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'CS701',
        'title' => 'Announcement Action Test',
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

    $data = [
        'title' => 'Midterm Exam Schedule',
        'body' => 'The midterm exam will be held next Monday.',
    ];

    $announcement = $action->execute($instructor, $section, $data);

    expect($announcement)->toBeInstanceOf(Announcement::class);
    expect($announcement->title)->toBe('Midterm Exam Schedule');
    expect($announcement->course_section_id)->toBe($section->id);
    expect($announcement->created_by)->toBe($instructor->id);

    $this->assertDatabaseHas('announcements', [
        'title' => 'Midterm Exam Schedule',
        'course_section_id' => $section->id,
        'created_by' => $instructor->id,
    ]);
});

it('dispatches SendAnnouncementNotification job after creating announcement', function () {
    Queue::fake();

    $action = new CreateAnnouncement;

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'CS702',
        'title' => 'Notification Dispatch Test',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'B',
        'instructor_id' => $instructor->id,
    ]);

    $data = [
        'title' => 'Final Exam Notice',
        'body' => 'Please prepare for the final exam.',
    ];

    $action->execute($instructor, $section, $data);

    Queue::assertPushed(SendAnnouncementNotification::class);
});
