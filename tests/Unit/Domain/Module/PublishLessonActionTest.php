<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Module\Actions\PublishLesson;
use App\Domain\Module\Models\Lesson;
use App\Domain\Module\Models\Module;
use App\Enums\UserRole;
use App\Models\User;

function makeLessonForPublish(bool $published = false): Lesson
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'PL'.fake()->unique()->numberBetween(100, 999),
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
        'published_at' => $published ? now()->subMinute() : null,
    ]);
}

test('it sets published_at when publishing an unpublished lesson', function (): void {
    $lesson = makeLessonForPublish(false);
    $action = new PublishLesson;

    $result = $action->handle($lesson);

    expect($result->published_at)->not->toBeNull();
});

test('it clears published_at when unpublishing a published lesson', function (): void {
    $lesson = makeLessonForPublish(true);
    $action = new PublishLesson;

    $result = $action->handle($lesson);

    expect($result->published_at)->toBeNull();
});

test('it persists publish state to the database', function (): void {
    $lesson = makeLessonForPublish(false);
    $action = new PublishLesson;

    $action->handle($lesson);

    $this->assertDatabaseHas('lessons', [
        'id' => $lesson->id,
    ]);
    expect(Lesson::find($lesson->id)->published_at)->not->toBeNull();
});
