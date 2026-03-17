<?php

declare(strict_types=1);

use App\Domain\Content\Actions\UpdateModule;
use App\Domain\Content\Models\Module;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;

it('updates module fields and returns the updated module', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'MOD102',
        'title' => 'Update Module Test',
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
        'title' => 'Original Module Title',
        'order_no' => 1,
    ]);

    $action = new UpdateModule;

    $updated = $action->execute($module, [
        'title' => 'Updated Module Title',
        'description' => 'New description.',
        'order_no' => 2,
        'published' => false,
    ]);

    expect($updated)->toBeInstanceOf(Module::class);
    expect($updated->title)->toBe('Updated Module Title');
    expect($updated->order_no)->toBe(2);

    $this->assertDatabaseHas('modules', [
        'id' => $module->id,
        'title' => 'Updated Module Title',
    ]);
});
