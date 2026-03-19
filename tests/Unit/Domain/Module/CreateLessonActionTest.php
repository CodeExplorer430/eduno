<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Module\Actions\CreateLesson;
use App\Domain\Module\Models\Lesson;
use App\Domain\Module\Models\Module;
use App\Enums\LessonType;
use App\Enums\UserRole;
use App\Models\User;

function makeModuleForCreateLesson(): Module
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'CL'.fake()->unique()->numberBetween(100, 999),
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

    return Module::create([
        'course_section_id' => $section->id,
        'title' => 'Test Module',
        'order_no' => 1,
    ]);
}

test('it creates a lesson with the correct type', function (): void {
    $module = makeModuleForCreateLesson();
    $action = new CreateLesson();

    $lesson = $action->handle($module, [
        'title' => 'Introduction',
        'type' => 'text',
        'content' => 'Some content',
    ]);

    expect($lesson)->toBeInstanceOf(Lesson::class)
        ->and($lesson->type)->toBe(LessonType::Text)
        ->and($lesson->title)->toBe('Introduction')
        ->and($lesson->content)->toBe('Some content');
});

test('it auto-increments order_no based on existing lessons', function (): void {
    $module = makeModuleForCreateLesson();

    Lesson::create([
        'module_id' => $module->id,
        'title' => 'Existing Lesson',
        'type' => 'text',
        'order_no' => 1,
    ]);

    $action = new CreateLesson();
    $lesson = $action->handle($module, ['title' => 'New Lesson', 'type' => 'text']);

    expect($lesson->order_no)->toBe(2);
});

test('it uses provided order_no when given', function (): void {
    $module = makeModuleForCreateLesson();
    $action = new CreateLesson();

    $lesson = $action->handle($module, [
        'title' => 'Lesson',
        'type' => 'pdf',
        'order_no' => 5,
    ]);

    expect($lesson->order_no)->toBe(5);
});

test('content is optional and defaults to null', function (): void {
    $module = makeModuleForCreateLesson();
    $action = new CreateLesson();

    $lesson = $action->handle($module, ['title' => 'Lesson', 'type' => 'text']);

    expect($lesson->content)->toBeNull();
});
