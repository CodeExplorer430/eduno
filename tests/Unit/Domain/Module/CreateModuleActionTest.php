<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Module\Actions\CreateModule;
use App\Domain\Module\Models\Module;
use App\Enums\UserRole;
use App\Models\User;

function makeTestSection(): CourseSection
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'MOD'.fake()->unique()->numberBetween(100, 999),
        'title' => 'Test Course',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    return CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'Section A',
        'instructor_id' => $instructor->id,
    ]);
}

test('it creates a module with valid data', function (): void {
    $section = makeTestSection();
    $action = new CreateModule;

    $module = $action->handle($section, [
        'title' => 'Week 1: Introduction',
        'description' => 'Overview of the subject',
    ]);

    expect($module)->toBeInstanceOf(Module::class)
        ->and($module->title)->toBe('Week 1: Introduction')
        ->and($module->course_section_id)->toBe($section->id)
        ->and($module->published_at)->toBeNull();
});

test('it auto-increments order_no from existing module count', function (): void {
    $section = makeTestSection();
    $action = new CreateModule;

    $first = $action->handle($section, ['title' => 'Module 1']);
    $second = $action->handle($section, ['title' => 'Module 2']);

    expect($first->order_no)->toBe(1)
        ->and($second->order_no)->toBe(2);
});

test('it respects explicit order_no when provided', function (): void {
    $section = makeTestSection();
    $action = new CreateModule;

    $module = $action->handle($section, ['title' => 'Module 5', 'order_no' => 5]);

    expect($module->order_no)->toBe(5);
});

test('description is optional and defaults to null', function (): void {
    $section = makeTestSection();
    $action = new CreateModule;

    $module = $action->handle($section, ['title' => 'No Description']);

    expect($module->description)->toBeNull();
});

test('it persists the module in the database', function (): void {
    $section = makeTestSection();
    $action = new CreateModule;

    $action->handle($section, ['title' => 'Stored Module']);

    $this->assertDatabaseHas('modules', [
        'course_section_id' => $section->id,
        'title' => 'Stored Module',
    ]);
});
