<?php

declare(strict_types=1);

use App\Domain\Content\Actions\CreateLesson;
use App\Domain\Content\Models\Lesson;
use App\Domain\Content\Models\Module;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;

it('creates a lesson with the correct module_id', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'LES101',
        'title' => 'Create Lesson Test',
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

    $action = new CreateLesson();

    $lesson = $action->execute($module->id, [
        'title' => 'Introduction Lesson',
        'type' => 'text',
        'content' => 'Welcome to the course!',
        'order_no' => 1,
        'published' => false,
    ]);

    expect($lesson)->toBeInstanceOf(Lesson::class);
    expect($lesson->module_id)->toBe($module->id);
    expect($lesson->title)->toBe('Introduction Lesson');
    expect($lesson->published_at)->toBeNull();

    $this->assertDatabaseHas('lessons', [
        'title' => 'Introduction Lesson',
        'module_id' => $module->id,
    ]);
});
