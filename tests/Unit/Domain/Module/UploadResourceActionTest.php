<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Module\Actions\UploadResource;
use App\Domain\Module\Models\Lesson;
use App\Domain\Module\Models\Module;
use App\Domain\Module\Models\Resource;
use App\Enums\ResourceVisibility;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function makeLessonForUpload(): Lesson
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'UPL'.fake()->unique()->numberBetween(100, 999),
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

    return Lesson::create([
        'module_id' => $module->id,
        'title' => 'Test Lesson',
        'type' => 'text',
        'order_no' => 1,
    ]);
}

test('it stores the file on the private disk', function (): void {
    Storage::fake('private');
    $lesson = makeLessonForUpload();
    $file = UploadedFile::fake()->create('lecture.pdf', 1024, 'application/pdf');
    $action = new UploadResource();

    $resource = $action->handle($lesson, $file, 'Lecture Notes', ResourceVisibility::Enrolled);

    Storage::disk('private')->assertExists($resource->file_path);
});

test('it generates a UUID-based filename not the original name', function (): void {
    Storage::fake('private');
    $lesson = makeLessonForUpload();
    $file = UploadedFile::fake()->create('my-secret-file.pdf', 512, 'application/pdf');
    $action = new UploadResource();

    $resource = $action->handle($lesson, $file, 'Notes', ResourceVisibility::Enrolled);

    expect($resource->file_path)->not->toContain('my-secret-file');
    expect($resource->file_path)->toEndWith('.pdf');
});

test('it records file metadata in the database', function (): void {
    Storage::fake('private');
    $lesson = makeLessonForUpload();
    $file = UploadedFile::fake()->create('slides.pdf', 2048, 'application/pdf');
    $action = new UploadResource();

    $resource = $action->handle($lesson, $file, 'Slides', ResourceVisibility::Enrolled);

    expect($resource)->toBeInstanceOf(Resource::class)
        ->and($resource->lesson_id)->toBe($lesson->id)
        ->and($resource->title)->toBe('Slides')
        ->and($resource->visibility)->toBe(ResourceVisibility::Enrolled);

    $this->assertDatabaseHas('resources', [
        'lesson_id' => $lesson->id,
        'title' => 'Slides',
    ]);
});
