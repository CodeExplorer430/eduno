<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Module\Actions\UpdateLesson;
use App\Domain\Module\Models\Lesson;
use App\Domain\Module\Models\Module;
use App\Enums\LessonType;
use App\Enums\UserRole;
use App\Models\User;

function makeLessonForUpdate(): Lesson
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'UL'.fake()->unique()->numberBetween(100, 999),
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
        'title' => 'Original Lesson',
        'type' => 'text',
        'order_no' => 1,
    ]);
}

test('it updates lesson title and content', function (): void {
    $lesson = makeLessonForUpdate();
    $action = new UpdateLesson();

    $updated = $action->handle($lesson, [
        'title' => 'Updated Title',
        'type' => 'text',
        'content' => 'Updated content',
    ]);

    expect($updated->title)->toBe('Updated Title')
        ->and($updated->content)->toBe('Updated content');
});

test('it updates the lesson type', function (): void {
    $lesson = makeLessonForUpdate();
    $action = new UpdateLesson();

    $updated = $action->handle($lesson, [
        'title' => 'Lesson',
        'type' => 'pdf',
    ]);

    expect($updated->type)->toBe(LessonType::Pdf);
});

test('it updates order_no when provided', function (): void {
    $lesson = makeLessonForUpdate();
    $action = new UpdateLesson();

    $updated = $action->handle($lesson, [
        'title' => 'Lesson',
        'type' => 'text',
        'order_no' => 3,
    ]);

    expect($updated->order_no)->toBe(3);
});

test('it persists changes to the database', function (): void {
    $lesson = makeLessonForUpdate();
    $action = new UpdateLesson();

    $action->handle($lesson, ['title' => 'Saved Title', 'type' => 'video']);

    $this->assertDatabaseHas('lessons', [
        'id' => $lesson->id,
        'title' => 'Saved Title',
    ]);
});
