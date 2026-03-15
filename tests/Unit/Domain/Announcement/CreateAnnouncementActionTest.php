<?php

declare(strict_types=1);

use App\Domain\Announcement\Actions\CreateAnnouncement;
use App\Domain\Announcement\Models\Announcement;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;

function makeAnnouncementTestSection(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'UCA'.fake()->unique()->numberBetween(100, 999),
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

    return [$instructor, $section];
}

test('it creates an announcement with correct fields', function (): void {
    [$instructor, $section] = makeAnnouncementTestSection();
    $action = new CreateAnnouncement;

    $announcement = $action->handle($section, [
        'title' => 'Test Announcement',
        'body' => 'Announcement body.',
    ], $instructor);

    expect($announcement)->toBeInstanceOf(Announcement::class)
        ->and($announcement->title)->toBe('Test Announcement')
        ->and($announcement->body)->toBe('Announcement body.')
        ->and($announcement->course_section_id)->toBe($section->id)
        ->and($announcement->created_by)->toBe($instructor->id)
        ->and($announcement->published_at)->toBeNull();
});

test('it persists the announcement in the database', function (): void {
    [$instructor, $section] = makeAnnouncementTestSection();
    $action = new CreateAnnouncement;

    $action->handle($section, ['title' => 'Stored', 'body' => 'Body'], $instructor);

    $this->assertDatabaseHas('announcements', [
        'course_section_id' => $section->id,
        'title' => 'Stored',
        'created_by' => $instructor->id,
    ]);
});
