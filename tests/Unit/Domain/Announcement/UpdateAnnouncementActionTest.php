<?php

declare(strict_types=1);

use App\Domain\Announcement\Actions\UpdateAnnouncement;
use App\Domain\Announcement\Models\Announcement;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;

function makeAnnouncementForUpdate(): Announcement
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'AN'.fake()->unique()->numberBetween(100, 999),
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

    return Announcement::create([
        'course_section_id' => $section->id,
        'title' => 'Original Title',
        'body' => 'Original body',
        'created_by' => $instructor->id,
    ]);
}

test('it updates announcement title', function (): void {
    $announcement = makeAnnouncementForUpdate();
    $action = new UpdateAnnouncement();

    $updated = $action->handle($announcement, [
        'title' => 'New Title',
        'body' => 'Original body',
    ]);

    expect($updated->title)->toBe('New Title');
});

test('it updates announcement body', function (): void {
    $announcement = makeAnnouncementForUpdate();
    $action = new UpdateAnnouncement();

    $updated = $action->handle($announcement, [
        'title' => 'Original Title',
        'body' => 'New body content',
    ]);

    expect($updated->body)->toBe('New body content');
});

test('it persists changes to the database', function (): void {
    $announcement = makeAnnouncementForUpdate();
    $action = new UpdateAnnouncement();

    $action->handle($announcement, ['title' => 'Saved Title', 'body' => 'Saved body']);

    $this->assertDatabaseHas('announcements', [
        'id' => $announcement->id,
        'title' => 'Saved Title',
    ]);
});
