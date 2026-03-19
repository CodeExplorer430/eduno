<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Module\Actions\UpdateModule;
use App\Domain\Module\Models\Module;
use App\Enums\UserRole;
use App\Models\User;

function makeModuleForUpdate(): Module
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'UM'.fake()->unique()->numberBetween(100, 999),
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
        'title' => 'Original Module',
        'description' => 'Old description',
        'order_no' => 1,
    ]);
}

test('it updates module title and description', function (): void {
    $module = makeModuleForUpdate();
    $action = new UpdateModule();

    $updated = $action->handle($module, [
        'title' => 'Updated Module',
        'description' => 'New description',
    ]);

    expect($updated->title)->toBe('Updated Module')
        ->and($updated->description)->toBe('New description');
});

test('it clears description when not provided', function (): void {
    $module = makeModuleForUpdate();
    $action = new UpdateModule();

    $updated = $action->handle($module, ['title' => 'Updated']);

    expect($updated->description)->toBeNull();
});

test('it updates order_no when provided', function (): void {
    $module = makeModuleForUpdate();
    $action = new UpdateModule();

    $updated = $action->handle($module, [
        'title' => 'Module',
        'order_no' => 5,
    ]);

    expect($updated->order_no)->toBe(5);
});

test('it persists changes to the database', function (): void {
    $module = makeModuleForUpdate();
    $action = new UpdateModule();

    $action->handle($module, ['title' => 'Persisted Title']);

    $this->assertDatabaseHas('modules', [
        'id' => $module->id,
        'title' => 'Persisted Title',
    ]);
});
