<?php

declare(strict_types=1);

use App\Domain\Content\Models\Lesson;
use App\Domain\Content\Models\Module;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Enums\UserRole;
use App\Models\User;

it('course show only returns published modules', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'PUB101',
        'title' => 'Published Filter Course',
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

    Module::create([
        'course_section_id' => $section->id,
        'title' => 'Published Module',
        'order_no' => 1,
        'published_at' => now(),
    ]);

    Module::create([
        'course_section_id' => $section->id,
        'title' => 'Draft Module',
        'order_no' => 2,
        'published_at' => null,
    ]);

    $response = $this->actingAs($student)->get(route('student.courses.show', $section));

    $response->assertStatus(200);
    $response->assertInertia(function ($page) {
        $page->component('Student/Courses/Show');
        $modules = $page->toArray()['props']['section']['modules'];
        expect(count($modules))->toBe(1);
        expect($modules[0]['title'])->toBe('Published Module');
    });
});

it('course show only returns published lessons within modules', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'PUB102',
        'title' => 'Lesson Filter Course',
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

    $module = Module::create([
        'course_section_id' => $section->id,
        'title' => 'Module 1',
        'order_no' => 1,
        'published_at' => now(),
    ]);

    Lesson::create([
        'module_id' => $module->id,
        'title' => 'Published Lesson',
        'type' => 'text',
        'order_no' => 1,
        'published_at' => now(),
    ]);

    Lesson::create([
        'module_id' => $module->id,
        'title' => 'Draft Lesson',
        'type' => 'text',
        'order_no' => 2,
        'published_at' => null,
    ]);

    $response = $this->actingAs($student)->get(route('student.courses.show', $section));

    $response->assertStatus(200);
    $response->assertInertia(function ($page) {
        $page->component('Student/Courses/Show');
        $modules = $page->toArray()['props']['section']['modules'];
        expect(count($modules[0]['lessons']))->toBe(1);
        expect($modules[0]['lessons'][0]['title'])->toBe('Published Lesson');
    });
});
