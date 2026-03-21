<?php

declare(strict_types=1);

use App\Domain\Announcement\Models\Announcement;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Enums\UserRole;
use App\Models\User;

it('student can view announcements from their enrolled sections', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'ANN101',
        'title' => 'Announcement Test Course',
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

    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);

    Announcement::create([
        'course_section_id' => $section->id,
        'title' => 'Published Announcement',
        'body' => 'Welcome students!',
        'published_at' => now(),
        'created_by' => $instructor->id,
    ]);

    $response = $this->actingAs($student)->get(route('student.announcements.index'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
            ->component('Student/Announcements/Index')
            ->has('announcements.data', 1)
    );
});

it('student cannot see announcements from sections they are not enrolled in', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'ANN102',
        'title' => 'Other Section Course',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    $otherSection = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'B',
        'instructor_id' => $instructor->id,
    ]);

    // Not enrolled in $otherSection
    Announcement::create([
        'course_section_id' => $otherSection->id,
        'title' => 'Other Section Announcement',
        'body' => 'You should not see this.',
        'published_at' => now(),
        'created_by' => $instructor->id,
    ]);

    $response = $this->actingAs($student)->get(route('student.announcements.index'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
            ->component('Student/Announcements/Index')
            ->has('announcements.data', 0)
    );
});

it('student cannot see unpublished announcements', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'ANN103',
        'title' => 'Unpublished Test Course',
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

    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);

    Announcement::create([
        'course_section_id' => $section->id,
        'title' => 'Draft Announcement',
        'body' => 'This is a draft.',
        'published_at' => null,
        'created_by' => $instructor->id,
    ]);

    $response = $this->actingAs($student)->get(route('student.announcements.index'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
            ->component('Student/Announcements/Index')
            ->has('announcements.data', 0)
    );
});

it('guest is redirected from student announcements', function () {
    $response = $this->get(route('student.announcements.index'));

    $response->assertRedirect(route('login'));
});
