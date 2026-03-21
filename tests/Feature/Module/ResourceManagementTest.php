<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Module\Models\Lesson;
use App\Domain\Module\Models\Module;
use App\Domain\Module\Models\Resource;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function makeSectionWithLessonForResource(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'RM'.fake()->unique()->numberBetween(100, 999),
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
        'published_at' => now()->subMinute(),
    ]);
    $lesson = Lesson::create([
        'module_id' => $module->id,
        'title' => 'Test Lesson',
        'type' => 'text',
        'order_no' => 1,
        'published_at' => now()->subMinute(),
    ]);

    return [$instructor, $section, $lesson];
}

function makeResourceForLesson(Lesson $lesson, string $visibility = 'enrolled'): Resource
{
    Storage::fake('private');
    $filePath = 'resources/test.pdf';
    Storage::disk('private')->put($filePath, 'content');

    return Resource::create([
        'lesson_id' => $lesson->id,
        'title' => 'Test Resource',
        'file_path' => $filePath,
        'mime_type' => 'application/pdf',
        'size_bytes' => 100,
        'visibility' => $visibility,
    ]);
}

test('unauthenticated user cannot download a resource', function (): void {
    [, , $lesson] = makeSectionWithLessonForResource();
    $resource = makeResourceForLesson($lesson);

    $this->get(route('resources.download', $resource))
        ->assertRedirect(route('login'));
});

test('returns 404 when downloading a non-existent resource', function (): void {
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->get(route('resources.download', 999999))
        ->assertNotFound();
});

test('non-enrolled student cannot download an enrolled-visibility resource', function (): void {
    [, , $lesson] = makeSectionWithLessonForResource();
    $resource = makeResourceForLesson($lesson, 'enrolled');

    $unenrolledStudent = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($unenrolledStudent)
        ->get(route('resources.download', $resource))
        ->assertForbidden();
});

test('enrolled student can download an enrolled-visibility resource', function (): void {
    [$instructor, $section, $lesson] = makeSectionWithLessonForResource();
    $resource = makeResourceForLesson($lesson, 'enrolled');

    $student = User::factory()->create(['role' => UserRole::Student]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);

    // The controller generates a temporary URL and redirects away — expect a redirect response
    $this->actingAs($student)
        ->get(route('resources.download', $resource))
        ->assertRedirect();
});

test('instructor-only resource is inaccessible to students', function (): void {
    [, , $lesson] = makeSectionWithLessonForResource();
    $resource = makeResourceForLesson($lesson, 'instructor');

    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->get(route('resources.download', $resource))
        ->assertForbidden();
});

test('resource upload fails with an invalid visibility value', function (): void {
    Storage::fake('private');
    [$instructor, , $lesson] = makeSectionWithLessonForResource();

    $this->actingAs($instructor)
        ->post(route('lessons.resources.store', $lesson), [
            'title' => 'Bad Visibility',
            'file' => UploadedFile::fake()->create('test.pdf', 100, 'application/pdf'),
            'visibility' => 'student',
        ])
        ->assertSessionHasErrors('visibility');
});
