<?php

declare(strict_types=1);

use App\Domain\Announcement\Actions\PublishAnnouncement;
use App\Domain\Announcement\Models\Announcement;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;

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
