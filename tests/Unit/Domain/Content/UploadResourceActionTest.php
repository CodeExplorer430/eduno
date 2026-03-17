<?php

declare(strict_types=1);

use App\Domain\Content\Actions\UploadResource;
use App\Domain\Content\Models\Lesson;
use App\Domain\Content\Models\Module;
use App\Domain\Content\Models\Resource;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('creates a Resource record and stores the file', function () {
    Storage::fake('private');

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'RES101',
        'title' => 'Upload Resource Test',
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

    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $action = new UploadResource;

    $resource = $action->execute($lesson->id, 'Course Syllabus', $file, 'students');

    expect($resource)->toBeInstanceOf(Resource::class);
    expect($resource->lesson_id)->toBe($lesson->id);
    expect($resource->title)->toBe('Course Syllabus');

    Storage::disk('private')->assertExists($resource->file_path);

    $this->assertDatabaseHas('resources', [
        'lesson_id' => $lesson->id,
        'title' => 'Course Syllabus',
    ]);
});
