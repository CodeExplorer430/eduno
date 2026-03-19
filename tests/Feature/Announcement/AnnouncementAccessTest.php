<?php

declare(strict_types=1);

use App\Domain\Announcement\Models\Announcement;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;

// ─── Tests ────────────────────────────────────────────────────────────────────

test('unenrolled student gets 403 on published announcement', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'AC'.fake()->unique()->numberBetween(100, 999),
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
    $announcement = Announcement::create([
        'course_section_id' => $section->id,
        'title' => 'Test Announcement',
        'body' => 'Announcement body.',
        'created_by' => $instructor->id,
        'published_at' => now()->subMinute(),
    ]);

    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->get(route('announcements.show', $announcement))
        ->assertForbidden();
});
