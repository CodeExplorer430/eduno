<?php

declare(strict_types=1);

use App\Domain\Content\Models\Module;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;

it('instructor can view modules for their section', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'MOD101',
        'title' => 'Module Test Course',
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

    $response = $this->actingAs($instructor)->get(
        route('instructor.courses.modules.index', $section)
    );

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('Instructor/Modules/Index'));
});

it('instructor can create a module', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'MOD102',
        'title' => 'Module Create Course',
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
        route('instructor.courses.modules.store', $section),
        [
            'title' => 'Week 1: Introduction',
            'description' => 'Getting started',
            'order_no' => 1,
            'published' => true,
        ]
    );

    $response->assertRedirect(route('instructor.courses.modules.index', $section));
    $this->assertDatabaseHas('modules', [
        'title' => 'Week 1: Introduction',
        'course_section_id' => $section->id,
    ]);
});

it('instructor can update their module', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'MOD103',
        'title' => 'Module Update Course',
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
        'title' => 'Old Title',
        'order_no' => 1,
    ]);

    $response = $this->actingAs($instructor)->patch(
        route('instructor.courses.modules.update', ['section' => $section, 'module' => $module]),
        [
            'title' => 'Updated Title',
            'order_no' => 1,
            'published' => false,
        ]
    );

    $response->assertRedirect(route('instructor.courses.modules.index', $section));
    $this->assertDatabaseHas('modules', ['id' => $module->id, 'title' => 'Updated Title']);
});

it('instructor can delete their module', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'MOD104',
        'title' => 'Module Delete Course',
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
        'title' => 'To Be Deleted',
        'order_no' => 1,
    ]);

    $response = $this->actingAs($instructor)->delete(
        route('instructor.courses.modules.destroy', ['section' => $section, 'module' => $module])
    );

    $response->assertRedirect(route('instructor.courses.modules.index', $section));
    $this->assertDatabaseMissing('modules', ['id' => $module->id]);
});

it('another instructor cannot modify a module they do not own', function () {
    $ownerInstructor = User::factory()->create(['role' => UserRole::Instructor]);
    $otherInstructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'MOD105',
        'title' => 'Ownership Test Course',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $ownerInstructor->id,
    ]);

    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'A',
        'instructor_id' => $ownerInstructor->id,
    ]);

    $module = Module::create([
        'course_section_id' => $section->id,
        'title' => 'Protected Module',
        'order_no' => 1,
    ]);

    $response = $this->actingAs($otherInstructor)->patch(
        route('instructor.courses.modules.update', ['section' => $section, 'module' => $module]),
        ['title' => 'Hacked', 'order_no' => 1, 'published' => false]
    );

    $response->assertStatus(403);
});

it('student cannot create a module', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'MOD106',
        'title' => 'Student Block Course',
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

    $response = $this->actingAs($student)->post(
        route('instructor.courses.modules.store', $section),
        ['title' => 'Injected Module', 'order_no' => 1, 'published' => false]
    );

    $response->assertStatus(403);
});
