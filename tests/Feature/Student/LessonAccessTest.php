<?php

declare(strict_types=1);

use App\Domain\Content\Models\Lesson;
use App\Domain\Content\Models\Module;
use App\Domain\Content\Models\Resource;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('enrolled student can view a published lesson', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'LAT101',
        'title' => 'Lesson Access Course',
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

    $lesson = Lesson::create([
        'module_id' => $module->id,
        'title' => 'Published Lesson',
        'type' => 'text',
        'content' => 'Hello world',
        'order_no' => 1,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($student)->get(route('student.lessons.show', $lesson));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page->component('Student/Lessons/Show')
    );
});

it('enrolled student gets 404 for unpublished lesson', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'LAT102',
        'title' => 'Lesson Access Course 2',
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

    $lesson = Lesson::create([
        'module_id' => $module->id,
        'title' => 'Draft Lesson',
        'type' => 'text',
        'order_no' => 1,
        'published_at' => null,
    ]);

    $response = $this->actingAs($student)->get(route('student.lessons.show', $lesson));

    $response->assertStatus(404);
});

it('unenrolled student gets 403 for published lesson', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'LAT103',
        'title' => 'Lesson Access Course 3',
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

    // No enrollment

    $module = Module::create([
        'course_section_id' => $section->id,
        'title' => 'Module 1',
        'order_no' => 1,
        'published_at' => now(),
    ]);

    $lesson = Lesson::create([
        'module_id' => $module->id,
        'title' => 'Published Lesson',
        'type' => 'text',
        'order_no' => 1,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($student)->get(route('student.lessons.show', $lesson));

    $response->assertStatus(403);
});

it('enrolled student can download a resource', function () {
    Storage::fake('private');

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'LAT104',
        'title' => 'Resource Download Course',
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

    $lesson = Lesson::create([
        'module_id' => $module->id,
        'title' => 'Lesson 1',
        'type' => 'document',
        'order_no' => 1,
        'published_at' => now(),
    ]);

    $fakePath = 'resources/test-file.pdf';
    Storage::disk('private')->put($fakePath, 'fake pdf content');

    $resource = Resource::create([
        'lesson_id' => $lesson->id,
        'title' => 'Lecture PDF',
        'file_path' => $fakePath,
        'mime_type' => 'application/pdf',
        'size_bytes' => 100,
        'visibility' => 'enrolled',
    ]);

    $response = $this->actingAs($student)->get(route('student.resources.download', $resource));

    $response->assertStatus(200);
});

it('unenrolled student gets 403 for resource download', function () {
    Storage::fake('private');

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'LAT105',
        'title' => 'Resource Block Course',
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

    // No enrollment

    $module = Module::create([
        'course_section_id' => $section->id,
        'title' => 'Module 1',
        'order_no' => 1,
        'published_at' => now(),
    ]);

    $lesson = Lesson::create([
        'module_id' => $module->id,
        'title' => 'Lesson 1',
        'type' => 'document',
        'order_no' => 1,
        'published_at' => now(),
    ]);

    $fakePath = 'resources/test-file2.pdf';
    Storage::disk('private')->put($fakePath, 'fake pdf content');

    $resource = Resource::create([
        'lesson_id' => $lesson->id,
        'title' => 'Blocked PDF',
        'file_path' => $fakePath,
        'mime_type' => 'application/pdf',
        'size_bytes' => 100,
        'visibility' => 'enrolled',
    ]);

    $response = $this->actingAs($student)->get(route('student.resources.download', $resource));

    $response->assertStatus(403);
});

it('guest is redirected from lesson show', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'LAT106',
        'title' => 'Guest Block Course',
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
        'published_at' => now(),
    ]);

    $lesson = Lesson::create([
        'module_id' => $module->id,
        'title' => 'Some Lesson',
        'type' => 'text',
        'order_no' => 1,
        'published_at' => now(),
    ]);

    $response = $this->get(route('student.lessons.show', $lesson));

    $response->assertRedirect(route('login'));
});
