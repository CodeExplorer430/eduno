<?php

declare(strict_types=1);

use App\Domain\Content\Actions\DeleteResource;
use App\Domain\Content\Models\Lesson;
use App\Domain\Content\Models\Module;
use App\Domain\Content\Models\Resource;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('deletes the resource record and removes the file from storage', function () {
    Storage::fake('private');

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'RES102',
        'title' => 'Delete Resource Test',
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
        'type' => 'text',
        'order_no' => 1,
    ]);

    Storage::disk('private')->put('resources/test-file.pdf', 'fake content');

    $resource = Resource::create([
        'lesson_id' => $lesson->id,
        'title' => 'Test File',
        'file_path' => 'resources/test-file.pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 100,
        'visibility' => 'students',
    ]);

    Storage::disk('private')->assertExists('resources/test-file.pdf');

    $action = new DeleteResource();
    $action->execute($resource);

    Storage::disk('private')->assertMissing('resources/test-file.pdf');
    $this->assertDatabaseMissing('resources', ['id' => $resource->id]);
});
