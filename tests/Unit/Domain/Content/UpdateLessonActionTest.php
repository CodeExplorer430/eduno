<?php

declare(strict_types=1);

use App\Domain\Content\Actions\UpdateLesson;
use App\Domain\Content\Models\Lesson;
use App\Domain\Content\Models\Module;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;

it('updates lesson fields and returns the updated lesson', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'LES102',
        'title' => 'Update Lesson Test',
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
        'title' => 'Original Lesson',
        'type' => 'text',
        'order_no' => 1,
    ]);

    $action = new UpdateLesson();

    $updated = $action->execute($lesson, [
        'title' => 'Updated Lesson',
        'type' => 'video',
        'content' => 'https://example.com/video',
        'order_no' => 2,
        'published' => false,
    ]);

    expect($updated)->toBeInstanceOf(Lesson::class);
    expect($updated->title)->toBe('Updated Lesson');
    expect($updated->type)->toBe('video');

    $this->assertDatabaseHas('lessons', [
        'id' => $lesson->id,
        'title' => 'Updated Lesson',
        'type' => 'video',
    ]);
});
