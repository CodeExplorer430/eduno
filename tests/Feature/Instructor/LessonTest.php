<?php

declare(strict_types=1);

use App\Domain\Content\Models\Lesson;
use App\Domain\Content\Models\Module;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;

it('instructor can create a lesson in their module', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'LES101',
        'title' => 'Lesson Test Course',
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

    $module = Module::create([
        'course_section_id' => $section->id,
        'title' => 'Module 1',
        'order_no' => 1,
    ]);

    $response = $this->actingAs($instructor)->post(
        route('instructor.courses.modules.lessons.store', ['section' => $section, 'module' => $module]),
        [
            'title' => 'Lesson 1',
            'type' => 'text',
            'content' => 'Some content here.',
            'order_no' => 1,
            'published' => true,
        ]
    );

    $response->assertRedirect(route('instructor.courses.modules.index', $section));
    $this->assertDatabaseHas('lessons', [
        'title' => 'Lesson 1',
        'module_id' => $module->id,
        'type' => 'text',
    ]);
});

it('instructor can update their lesson', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'LES102',
        'title' => 'Lesson Update Course',
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

    $module = Module::create([
        'course_section_id' => $section->id,
        'title' => 'Module 1',
        'order_no' => 1,
    ]);

    $lesson = Lesson::create([
        'module_id' => $module->id,
        'title' => 'Old Lesson',
        'type' => 'text',
        'order_no' => 1,
    ]);

    $response = $this->actingAs($instructor)->patch(
        route('instructor.courses.modules.lessons.update', ['section' => $section, 'module' => $module, 'lesson' => $lesson]),
        ['title' => 'Updated Lesson', 'type' => 'video', 'order_no' => 1, 'published' => false]
    );

    $response->assertRedirect(route('instructor.courses.modules.index', $section));
    $this->assertDatabaseHas('lessons', ['id' => $lesson->id, 'title' => 'Updated Lesson', 'type' => 'video']);
});

it('student cannot create a lesson', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'LES103',
        'title' => 'Lesson Block Course',
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

    $module = Module::create([
        'course_section_id' => $section->id,
        'title' => 'Module 1',
        'order_no' => 1,
    ]);

    $response = $this->actingAs($student)->post(
        route('instructor.courses.modules.lessons.store', ['section' => $section, 'module' => $module]),
        ['title' => 'Injected Lesson', 'type' => 'text', 'order_no' => 1, 'published' => false]
    );

    $response->assertStatus(403);
});
