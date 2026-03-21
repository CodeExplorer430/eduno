<?php

declare(strict_types=1);

use App\Domain\Content\Models\Lesson;
use App\Domain\Content\Models\Module;
use App\Domain\Content\Models\Resource;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('instructor can upload a resource', function () {
    Storage::fake('private');

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'RES101',
        'title' => 'Resource Test Course',
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
        'title' => 'Lesson 1',
        'type' => 'document',
        'order_no' => 1,
    ]);

    $file = UploadedFile::fake()->create('lecture.pdf', 1024, 'application/pdf');

    $response = $this->actingAs($instructor)->post(
        route('instructor.courses.modules.lessons.resources.store', [
            'section' => $section,
            'module' => $module,
            'lesson' => $lesson,
        ]),
        [
            'title' => 'Lecture Slides',
            'file' => $file,
            'visibility' => 'enrolled',
        ]
    );

    $response->assertRedirect(route('instructor.courses.modules.index', $section));
    $this->assertDatabaseHas('resources', [
        'lesson_id' => $lesson->id,
        'title' => 'Lecture Slides',
        'visibility' => 'enrolled',
    ]);
});

it('instructor can delete a resource', function () {
    Storage::fake('private');

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'RES102',
        'title' => 'Resource Delete Course',
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
        'title' => 'Lesson 1',
        'type' => 'document',
        'order_no' => 1,
    ]);

    $fakePath = 'resources/test-file.pdf';
    Storage::disk('private')->put($fakePath, 'fake content');

    $resource = Resource::create([
        'lesson_id' => $lesson->id,
        'title' => 'Old Resource',
        'file_path' => $fakePath,
        'mime_type' => 'application/pdf',
        'size_bytes' => 100,
        'visibility' => 'enrolled',
    ]);

    $response = $this->actingAs($instructor)->delete(
        route('instructor.courses.modules.lessons.resources.destroy', [
            'section' => $section,
            'module' => $module,
            'lesson' => $lesson,
            'resource' => $resource,
        ])
    );

    $response->assertRedirect(route('instructor.courses.modules.index', $section));
    $this->assertDatabaseMissing('resources', ['id' => $resource->id]);
    Storage::disk('private')->assertMissing($fakePath);
});

it('student cannot upload a resource', function () {
    Storage::fake('private');

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'RES103',
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

    $module = Module::create([
        'course_section_id' => $section->id,
        'title' => 'Module 1',
        'order_no' => 1,
    ]);

    $lesson = Lesson::create([
        'module_id' => $module->id,
        'title' => 'Lesson 1',
        'type' => 'document',
        'order_no' => 1,
    ]);

    $file = UploadedFile::fake()->create('lecture.pdf', 1024, 'application/pdf');

    $response = $this->actingAs($student)->post(
        route('instructor.courses.modules.lessons.resources.store', [
            'section' => $section,
            'module' => $module,
            'lesson' => $lesson,
        ]),
        [
            'title' => 'Hacked Resource',
            'file' => $file,
            'visibility' => 'enrolled',
        ]
    );

    $response->assertStatus(403);
});
