<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Module\Models\Lesson;
use App\Domain\Module\Models\Module;
use App\Enums\UserRole;
use App\Models\User;

function makeInstructorSectionForLesson(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'LM'.fake()->unique()->numberBetween(100, 999),
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

function makeModuleForLesson(CourseSection $section, bool $published = false): Module
{
    return Module::create([
        'course_section_id' => $section->id,
        'title' => 'Test Module',
        'order_no' => 1,
        'published_at' => $published ? now()->subMinute() : null,
    ]);
}

function makeLessonInModule(Module $module, bool $published = false): Lesson
{
    return Lesson::create([
        'module_id' => $module->id,
        'title' => 'Test Lesson',
        'type' => 'text',
        'order_no' => 1,
        'published_at' => $published ? now()->subMinute() : null,
    ]);
}

function enrollStudentForLesson(User $student, CourseSection $section): void
{
    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);
}

// ─── Authentication ───────────────────────────────────────────────────────────

test('unauthenticated user is redirected from lesson create', function (): void {
    [, $section] = makeInstructorSectionForLesson();
    $module = makeModuleForLesson($section);

    $this->get(route('modules.lessons.create', $module))
        ->assertRedirect(route('login'));
});

// ─── Lesson Create ────────────────────────────────────────────────────────────

test('instructor can create a lesson', function (): void {
    [$instructor, $section] = makeInstructorSectionForLesson();
    $module = makeModuleForLesson($section);

    $this->actingAs($instructor)
        ->post(route('modules.lessons.store', $module), [
            'title' => 'New Lesson',
            'type' => 'text',
            'content' => 'Some content',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('lessons', [
        'module_id' => $module->id,
        'title' => 'New Lesson',
    ]);
});

test('student cannot create a lesson', function (): void {
    [, $section] = makeInstructorSectionForLesson();
    $module = makeModuleForLesson($section);
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentForLesson($student, $section);

    $this->actingAs($student)
        ->post(route('modules.lessons.store', $module), [
            'title' => 'Hacked Lesson',
            'type' => 'text',
        ])
        ->assertForbidden();
});

test('lesson creation fails with missing title', function (): void {
    [$instructor, $section] = makeInstructorSectionForLesson();
    $module = makeModuleForLesson($section);

    $this->actingAs($instructor)
        ->post(route('modules.lessons.store', $module), [
            'type' => 'text',
        ])
        ->assertSessionHasErrors('title');
});

test('lesson creation fails with invalid type', function (): void {
    [$instructor, $section] = makeInstructorSectionForLesson();
    $module = makeModuleForLesson($section);

    $this->actingAs($instructor)
        ->post(route('modules.lessons.store', $module), [
            'title' => 'Lesson',
            'type' => 'invalid_type',
        ])
        ->assertSessionHasErrors('type');
});

// ─── Lesson Update ────────────────────────────────────────────────────────────

test('instructor can update their lesson', function (): void {
    [$instructor, $section] = makeInstructorSectionForLesson();
    $module = makeModuleForLesson($section);
    $lesson = makeLessonInModule($module);

    $this->actingAs($instructor)
        ->put(route('lessons.update', $lesson), [
            'title' => 'Updated Lesson',
            'type' => 'text',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('lessons', [
        'id' => $lesson->id,
        'title' => 'Updated Lesson',
    ]);
});

test('instructor cannot update another instructor\'s lesson', function (): void {
    [, $section] = makeInstructorSectionForLesson();
    $module = makeModuleForLesson($section);
    $lesson = makeLessonInModule($module);

    $otherInstructor = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($otherInstructor)
        ->put(route('lessons.update', $lesson), [
            'title' => 'Hijacked',
            'type' => 'text',
        ])
        ->assertForbidden();
});

// ─── Lesson Delete ────────────────────────────────────────────────────────────

test('instructor can delete their lesson', function (): void {
    [$instructor, $section] = makeInstructorSectionForLesson();
    $module = makeModuleForLesson($section);
    $lesson = makeLessonInModule($module);

    $this->actingAs($instructor)
        ->delete(route('lessons.destroy', $lesson))
        ->assertRedirect();

    $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);
});

test('student cannot delete a lesson', function (): void {
    [, $section] = makeInstructorSectionForLesson();
    $module = makeModuleForLesson($section);
    $lesson = makeLessonInModule($module);
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentForLesson($student, $section);

    $this->actingAs($student)
        ->delete(route('lessons.destroy', $lesson))
        ->assertForbidden();
});

// ─── Lesson Publish ───────────────────────────────────────────────────────────

test('instructor can publish a lesson', function (): void {
    [$instructor, $section] = makeInstructorSectionForLesson();
    $module = makeModuleForLesson($section);
    $lesson = makeLessonInModule($module, false);

    $this->actingAs($instructor)
        ->post(route('lessons.publish', $lesson))
        ->assertRedirect();

    expect(Lesson::find($lesson->id)->published_at)->not->toBeNull();
});

test('instructor can unpublish a published lesson', function (): void {
    [$instructor, $section] = makeInstructorSectionForLesson();
    $module = makeModuleForLesson($section);
    $lesson = makeLessonInModule($module, true);

    $this->actingAs($instructor)
        ->post(route('lessons.publish', $lesson))
        ->assertRedirect();

    expect(Lesson::find($lesson->id)->published_at)->toBeNull();
});

test('student cannot publish a lesson', function (): void {
    [, $section] = makeInstructorSectionForLesson();
    $module = makeModuleForLesson($section);
    $lesson = makeLessonInModule($module);
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentForLesson($student, $section);

    $this->actingAs($student)
        ->post(route('lessons.publish', $lesson))
        ->assertForbidden();
});
