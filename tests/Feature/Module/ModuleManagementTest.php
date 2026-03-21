<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Module\Actions\UploadResource;
use App\Domain\Module\Models\Lesson;
use App\Domain\Module\Models\Module;
use App\Domain\Module\Models\Resource;
use App\Enums\ResourceVisibility;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// ─── Helpers ────────────────────────────────────────────────────────────────

function makeInstructorSection(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'TST'.fake()->unique()->numberBetween(100, 999),
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

    return [$instructor, $section];
}

function makeModule(CourseSection $section, bool $published = false): Module
{
    return Module::create([
        'course_section_id' => $section->id,
        'title' => 'Test Module',
        'order_no' => 1,
        'published_at' => $published ? now()->subMinute() : null,
    ]);
}

function makeLesson(Module $module, bool $published = false): Lesson
{
    return Lesson::create([
        'module_id' => $module->id,
        'title' => 'Test Lesson',
        'type' => 'text',
        'order_no' => 1,
        'published_at' => $published ? now()->subMinute() : null,
    ]);
}

function enrollStudent(User $student, CourseSection $section): void
{
    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);
}

// ─── Module CRUD ─────────────────────────────────────────────────────────────

test('unauthenticated user is redirected from modules index', function (): void {
    [, $section] = makeInstructorSection();

    $this->get(route('sections.modules.index', $section))
        ->assertRedirect(route('login'));
});

test('instructor can view modules index for their section', function (): void {
    [$instructor, $section] = makeInstructorSection();

    $this->actingAs($instructor)
        ->get(route('sections.modules.index', $section))
        ->assertOk();
});

test('instructor can create a module', function (): void {
    [$instructor, $section] = makeInstructorSection();

    $this->actingAs($instructor)
        ->post(route('sections.modules.store', $section), [
            'title' => 'New Module',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('modules', [
        'course_section_id' => $section->id,
        'title' => 'New Module',
    ]);
});

test('student cannot create a module', function (): void {
    [, $section] = makeInstructorSection();
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->post(route('sections.modules.store', $section), ['title' => 'Hack'])
        ->assertForbidden();
});

test('instructor cannot create module in another instructor section', function (): void {
    [, $section] = makeInstructorSection();
    $other = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($other)
        ->post(route('sections.modules.store', $section), ['title' => 'Hack'])
        ->assertForbidden();
});

test('instructor can update their module', function (): void {
    [$instructor, $section] = makeInstructorSection();
    $module = makeModule($section);

    $this->actingAs($instructor)
        ->put(route('modules.update', $module), ['title' => 'Updated Title'])
        ->assertRedirect();

    $this->assertDatabaseHas('modules', ['id' => $module->id, 'title' => 'Updated Title']);
});

test('instructor can delete their module', function (): void {
    [$instructor, $section] = makeInstructorSection();
    $module = makeModule($section);

    $this->actingAs($instructor)
        ->delete(route('modules.destroy', $module))
        ->assertRedirect();

    $this->assertDatabaseMissing('modules', ['id' => $module->id]);
});

test('module creation fails with missing title', function (): void {
    [$instructor, $section] = makeInstructorSection();

    $this->actingAs($instructor)
        ->post(route('sections.modules.store', $section), [])
        ->assertSessionHasErrors(['title']);
});

// ─── Module visibility ────────────────────────────────────────────────────────

test('instructor sees unpublished modules', function (): void {
    [$instructor, $section] = makeInstructorSection();
    makeModule($section, false);

    $this->actingAs($instructor)
        ->get(route('sections.modules.index', $section))
        ->assertOk();
});

test('instructor can publish a module', function (): void {
    [$instructor, $section] = makeInstructorSection();
    $module = makeModule($section);

    $this->actingAs($instructor)
        ->post(route('modules.publish', $module))
        ->assertRedirect();

    expect($module->fresh()->published_at)->not->toBeNull();
});

test('instructor can unpublish a published module', function (): void {
    [$instructor, $section] = makeInstructorSection();
    $module = makeModule($section, true);

    $this->actingAs($instructor)
        ->post(route('modules.publish', $module))
        ->assertRedirect();

    expect($module->fresh()->published_at)->toBeNull();
});

// ─── Lesson CRUD ─────────────────────────────────────────────────────────────

test('instructor can create a lesson under a module', function (): void {
    [$instructor, $section] = makeInstructorSection();
    $module = makeModule($section);

    $this->actingAs($instructor)
        ->post(route('modules.lessons.store', $module), [
            'title' => 'Lesson 1',
            'type' => 'text',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('lessons', [
        'module_id' => $module->id,
        'title' => 'Lesson 1',
    ]);
});

test('instructor can update a lesson', function (): void {
    [$instructor, $section] = makeInstructorSection();
    $module = makeModule($section);
    $lesson = makeLesson($module);

    $this->actingAs($instructor)
        ->put(route('lessons.update', $lesson), [
            'title' => 'Updated Lesson',
            'type' => 'pdf',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('lessons', ['id' => $lesson->id, 'title' => 'Updated Lesson']);
});

test('instructor can delete a lesson', function (): void {
    [$instructor, $section] = makeInstructorSection();
    $module = makeModule($section);
    $lesson = makeLesson($module);

    $this->actingAs($instructor)
        ->delete(route('lessons.destroy', $lesson))
        ->assertRedirect();

    $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);
});

test('student cannot create a lesson', function (): void {
    [, $section] = makeInstructorSection();
    $module = makeModule($section);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->post(route('modules.lessons.store', $module), ['title' => 'Hack', 'type' => 'text'])
        ->assertForbidden();
});

test('lesson creation fails with invalid type', function (): void {
    [$instructor, $section] = makeInstructorSection();
    $module = makeModule($section);

    $this->actingAs($instructor)
        ->post(route('modules.lessons.store', $module), [
            'title' => 'Bad Lesson',
            'type' => 'unknown',
        ])
        ->assertSessionHasErrors(['type']);
});

// ─── Lesson visibility ────────────────────────────────────────────────────────

test('enrolled student can view a published lesson', function (): void {
    [$instructor, $section] = makeInstructorSection();
    $module = makeModule($section, true);
    $lesson = makeLesson($module, true);
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudent($student, $section);

    $this->actingAs($student)
        ->get(route('lessons.show', $lesson))
        ->assertOk();
});

test('enrolled student cannot view an unpublished lesson', function (): void {
    [$instructor, $section] = makeInstructorSection();
    $module = makeModule($section, true);
    $lesson = makeLesson($module, false);
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudent($student, $section);

    $this->actingAs($student)
        ->get(route('lessons.show', $lesson))
        ->assertForbidden();
});

test('instructor can view unpublished lesson', function (): void {
    [$instructor, $section] = makeInstructorSection();
    $module = makeModule($section);
    $lesson = makeLesson($module, false);

    $this->actingAs($instructor)
        ->get(route('lessons.show', $lesson))
        ->assertOk();
});

// ─── Resource upload ─────────────────────────────────────────────────────────

test('instructor can upload a resource', function (): void {
    Storage::fake('private');
    [$instructor, $section] = makeInstructorSection();
    $module = makeModule($section);
    $lesson = makeLesson($module);
    $file = UploadedFile::fake()->create('notes.pdf', 512, 'application/pdf');

    $this->actingAs($instructor)
        ->post(route('lessons.resources.store', $lesson), [
            'title' => 'Lecture Notes',
            'file' => $file,
            'visibility' => 'enrolled',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('resources', [
        'lesson_id' => $lesson->id,
        'title' => 'Lecture Notes',
    ]);
});

test('student cannot upload a resource', function (): void {
    Storage::fake('private');
    [, $section] = makeInstructorSection();
    $module = makeModule($section);
    $lesson = makeLesson($module);
    $student = User::factory()->create(['role' => UserRole::Student]);
    $file = UploadedFile::fake()->create('hack.pdf', 512, 'application/pdf');

    $this->actingAs($student)
        ->post(route('lessons.resources.store', $lesson), [
            'title' => 'Hack',
            'file' => $file,
            'visibility' => 'enrolled',
        ])
        ->assertForbidden();
});

test('resource upload fails for invalid mime type', function (): void {
    Storage::fake('private');
    [$instructor, $section] = makeInstructorSection();
    $module = makeModule($section);
    $lesson = makeLesson($module);
    $file = UploadedFile::fake()->create('virus.exe', 512, 'application/x-msdownload');

    $this->actingAs($instructor)
        ->post(route('lessons.resources.store', $lesson), [
            'title' => 'Bad File',
            'file' => $file,
            'visibility' => 'enrolled',
        ])
        ->assertSessionHasErrors(['file']);
});

test('instructor can delete a resource', function (): void {
    Storage::fake('private');
    [$instructor, $section] = makeInstructorSection();
    $module = makeModule($section);
    $lesson = makeLesson($module);
    $lesson->load('module');

    $uploadAction = new UploadResource();
    $file = UploadedFile::fake()->create('notes.pdf', 512, 'application/pdf');
    $resource = $uploadAction->handle($lesson, $file, 'Notes', ResourceVisibility::Enrolled);

    $this->actingAs($instructor)
        ->delete(route('resources.destroy', $resource))
        ->assertRedirect();

    $this->assertDatabaseMissing('resources', ['id' => $resource->id]);
});

test('file is deleted from private disk when resource is destroyed', function (): void {
    Storage::fake('private');
    [$instructor, $section] = makeInstructorSection();
    $module = makeModule($section);
    $lesson = makeLesson($module);
    $lesson->load('module');

    $uploadAction = new UploadResource();
    $file = UploadedFile::fake()->create('notes.pdf', 512, 'application/pdf');
    $resource = $uploadAction->handle($lesson, $file, 'Notes', ResourceVisibility::Enrolled);

    $filePath = $resource->file_path;
    Storage::disk('private')->assertExists($filePath);

    $this->actingAs($instructor)
        ->delete(route('resources.destroy', $resource));

    Storage::disk('private')->assertMissing($filePath);
});

// ─── Student access ───────────────────────────────────────────────────────────

test('enrolled student can access enrolled-visibility resource download', function (): void {
    Storage::fake('private');
    [$instructor, $section] = makeInstructorSection();
    $module = makeModule($section, true);
    $lesson = makeLesson($module, true);
    $lesson->load('module');
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudent($student, $section);

    $uploadAction = new UploadResource();
    $file = UploadedFile::fake()->create('notes.pdf', 512, 'application/pdf');
    $resource = $uploadAction->handle($lesson, $file, 'Notes', ResourceVisibility::Enrolled);

    $this->actingAs($student)
        ->get(route('resources.download', $resource))
        ->assertRedirect();
});

test('enrolled student cannot access instructor-only resource', function (): void {
    Storage::fake('private');
    [$instructor, $section] = makeInstructorSection();
    $module = makeModule($section, true);
    $lesson = makeLesson($module, true);
    $lesson->load('module');
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudent($student, $section);

    $uploadAction = new UploadResource();
    $file = UploadedFile::fake()->create('answer-key.pdf', 512, 'application/pdf');
    $resource = $uploadAction->handle($lesson, $file, 'Answer Key', ResourceVisibility::Instructor);

    $this->actingAs($student)
        ->get(route('resources.download', $resource))
        ->assertForbidden();
});

// ─── Ownership (non-owning instructor) ───────────────────────────────────────

test('non-owning instructor cannot update another instructor\'s module', function (): void {
    [, $section] = makeInstructorSection();
    $module = makeModule($section);
    $other = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($other)
        ->put(route('modules.update', $module), ['title' => 'Hijack'])
        ->assertForbidden();
});

test('non-owning instructor cannot delete another instructor\'s module', function (): void {
    [, $section] = makeInstructorSection();
    $module = makeModule($section);
    $other = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($other)
        ->delete(route('modules.destroy', $module))
        ->assertForbidden();
});

test('non-owning instructor cannot publish another instructor\'s module', function (): void {
    [, $section] = makeInstructorSection();
    $module = makeModule($section, false);
    $other = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($other)
        ->post(route('modules.publish', $module))
        ->assertForbidden();
});

// ─── Role gates ───────────────────────────────────────────────────────────────

test('student cannot update a module', function (): void {
    [, $section] = makeInstructorSection();
    $module = makeModule($section);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->put(route('modules.update', $module), ['title' => 'Hack'])
        ->assertForbidden();
});

test('student cannot delete a module', function (): void {
    [, $section] = makeInstructorSection();
    $module = makeModule($section);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->delete(route('modules.destroy', $module))
        ->assertForbidden();
});

test('student cannot publish a module', function (): void {
    [, $section] = makeInstructorSection();
    $module = makeModule($section, false);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->post(route('modules.publish', $module))
        ->assertForbidden();
});

// ─── Ownership (non-owning instructor) — Lesson ───────────────────────────────

test('non-owning instructor cannot create a lesson in another instructor\'s module', function (): void {
    [, $section] = makeInstructorSection();
    $module = makeModule($section);
    $other = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($other)
        ->post(route('modules.lessons.store', $module), ['title' => 'Hijack', 'type' => 'text'])
        ->assertForbidden();
});

test('non-owning instructor cannot update another instructor\'s lesson', function (): void {
    [, $section] = makeInstructorSection();
    $module = makeModule($section);
    $lesson = makeLesson($module);
    $other = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($other)
        ->put(route('lessons.update', $lesson), ['title' => 'Hijack', 'type' => 'text'])
        ->assertForbidden();
});

test('non-owning instructor cannot delete another instructor\'s lesson', function (): void {
    [, $section] = makeInstructorSection();
    $module = makeModule($section);
    $lesson = makeLesson($module);
    $other = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($other)
        ->delete(route('lessons.destroy', $lesson))
        ->assertForbidden();
});

test('non-owning instructor cannot publish another instructor\'s lesson', function (): void {
    [, $section] = makeInstructorSection();
    $module = makeModule($section);
    $lesson = makeLesson($module, false);
    $other = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($other)
        ->post(route('lessons.publish', $lesson))
        ->assertForbidden();
});

// ─── Role gates — Lesson ──────────────────────────────────────────────────────

test('student cannot update a lesson', function (): void {
    [, $section] = makeInstructorSection();
    $module = makeModule($section);
    $lesson = makeLesson($module);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->put(route('lessons.update', $lesson), ['title' => 'Hack', 'type' => 'text'])
        ->assertForbidden();
});

test('student cannot delete a lesson', function (): void {
    [, $section] = makeInstructorSection();
    $module = makeModule($section);
    $lesson = makeLesson($module);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->delete(route('lessons.destroy', $lesson))
        ->assertForbidden();
});

test('student cannot publish a lesson', function (): void {
    [, $section] = makeInstructorSection();
    $module = makeModule($section);
    $lesson = makeLesson($module, false);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->post(route('lessons.publish', $lesson))
        ->assertForbidden();
});
