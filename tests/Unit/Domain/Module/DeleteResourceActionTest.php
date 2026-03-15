<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Module\Actions\DeleteResource;
use App\Domain\Module\Models\Lesson;
use App\Domain\Module\Models\Module;
use App\Domain\Module\Models\Resource;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

function makeResource(): Resource
{
    Storage::fake('private');

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'DR'.fake()->unique()->numberBetween(100, 999),
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
    $module = Module::create([
        'course_section_id' => $section->id,
        'title' => 'Test Module',
        'order_no' => 1,
    ]);
    $lesson = Lesson::create([
        'module_id' => $module->id,
        'title' => 'Test Lesson',
        'type' => 'text',
        'order_no' => 1,
    ]);

    $filePath = 'resources/test-file.pdf';
    Storage::disk('private')->put($filePath, 'fake content');

    return Resource::create([
        'lesson_id' => $lesson->id,
        'title' => 'Test Resource',
        'file_path' => $filePath,
        'mime_type' => 'application/pdf',
        'size_bytes' => 100,
        'visibility' => 'enrolled',
    ]);
}

test('it deletes the database record', function (): void {
    $resource = makeResource();
    $resourceId = $resource->id;

    $action = new DeleteResource;
    $action->handle($resource);

    $this->assertDatabaseMissing('resources', ['id' => $resourceId]);
});

test('it deletes the file from the private disk', function (): void {
    $resource = makeResource();
    $filePath = $resource->file_path;

    $action = new DeleteResource;
    $action->handle($resource);

    Storage::disk('private')->assertMissing($filePath);
});
