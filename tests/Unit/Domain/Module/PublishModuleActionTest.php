<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Module\Actions\PublishModule;
use App\Domain\Module\Models\Module;
use App\Enums\UserRole;
use App\Models\User;

function makeUnpublishedModule(): Module
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'PUB'.fake()->unique()->numberBetween(100, 999),
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

test('it publishes an unpublished module by setting published_at', function (): void {
    $module = makeUnpublishedModule();
    $action = new PublishModule;

    expect($module->published_at)->toBeNull();

    $updated = $action->handle($module);

    expect($updated->published_at)->not->toBeNull();
    $this->assertDatabaseHas('modules', [
        'id' => $module->id,
    ]);
    expect($updated->fresh()->published_at)->not->toBeNull();
});

test('it unpublishes a published module by clearing published_at', function (): void {
    $module = makeUnpublishedModule();
    $module->published_at = now()->subMinute();
    $module->save();

    $action = new PublishModule;
    $updated = $action->handle($module);

    expect($updated->published_at)->toBeNull();
    expect($updated->fresh()->published_at)->toBeNull();
});
