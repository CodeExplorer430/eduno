<?php

declare(strict_types=1);

use App\Domain\Announcement\Models\Announcement;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;

it('instructor can create an announcement', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'CS201',
        'title' => 'Instructor Course',
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

    $response = $this->actingAs($instructor)->post(
        route('instructor.announcements.store'),
        [
            'title' => 'Welcome to the Course',
            'body' => 'Hello students, welcome aboard!',
            'course_section_id' => $section->id,
        ]
    );

    $response->assertRedirect(route('instructor.announcements.index'));
    $this->assertDatabaseHas('announcements', [
        'title' => 'Welcome to the Course',
        'course_section_id' => $section->id,
        'created_by' => $instructor->id,
    ]);
});

it('instructor can delete their own announcement', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'CS202',
        'title' => 'Instructor Course 2',
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

    $announcement = Announcement::create([
        'course_section_id' => $section->id,
        'title' => 'Old Announcement',
        'body' => 'This will be deleted.',
        'published_at' => now(),
        'created_by' => $instructor->id,
    ]);

    $response = $this->actingAs($instructor)->delete(
        route('instructor.announcements.destroy', $announcement)
    );

    $response->assertRedirect(route('instructor.announcements.index'));
    $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
});

it('student cannot create an announcement', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'CS203',
        'title' => 'Student Block Test',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'C',
        'instructor_id' => $instructor->id,
    ]);

    $response = $this->actingAs($student)->post(
        route('instructor.announcements.store'),
        [
            'title' => 'Sneaky Announcement',
            'body' => 'I should not be allowed to do this.',
            'course_section_id' => $section->id,
        ]
    );

    $response->assertStatus(403);
});

it('guest is redirected when accessing announcement store', function () {
    $response = $this->post(
        route('instructor.announcements.store'),
        [
            'title' => 'Unauthorized',
            'body' => 'Should redirect.',
            'course_section_id' => 1,
        ]
    );

    $response->assertRedirect(route('login'));
});

it('instructor can update their own announcement', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'CS204',
        'title' => 'Announcement Update Course',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'D',
        'instructor_id' => $instructor->id,
    ]);

    $announcement = Announcement::create([
        'course_section_id' => $section->id,
        'title' => 'Original Title',
        'body' => 'Original body content.',
        'published_at' => now(),
        'created_by' => $instructor->id,
    ]);

    $response = $this->actingAs($instructor)->patch(
        route('instructor.announcements.update', $announcement),
        [
            'title' => 'Updated Title',
            'body' => 'Updated body content.',
            'course_section_id' => $section->id,
        ]
    );

    $response->assertRedirect(route('instructor.announcements.index'));
    $this->assertDatabaseHas('announcements', ['id' => $announcement->id, 'title' => 'Updated Title']);
});

it('non-owner instructor cannot update announcement', function () {
    $owner = User::factory()->create(['role' => UserRole::Instructor]);
    $other = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'CS205',
        'title' => 'Announcement Block Course',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $owner->id,
    ]);

    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'E',
        'instructor_id' => $owner->id,
    ]);

    $announcement = Announcement::create([
        'course_section_id' => $section->id,
        'title' => 'Owner Announcement',
        'body' => 'Should not be updated by other.',
        'published_at' => now(),
        'created_by' => $owner->id,
    ]);

    $response = $this->actingAs($other)->patch(
        route('instructor.announcements.update', $announcement),
        [
            'title' => 'Hijacked Title',
            'body' => 'Unauthorized update.',
            'course_section_id' => $section->id,
        ]
    );

    $response->assertStatus(403);
});
