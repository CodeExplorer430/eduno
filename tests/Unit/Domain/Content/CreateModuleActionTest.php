<?php

declare(strict_types=1);

use App\Domain\Content\Actions\CreateModule;
use App\Domain\Content\Models\Module;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;

it('creates a module with the correct course_section_id', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'MOD101',
        'title' => 'Create Module Test',
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

    $action = new CreateModule();

    $module = $action->execute($section->id, [
        'title' => 'Week 1: Introduction',
        'description' => 'Introduction to the course.',
        'order_no' => 1,
        'published' => false,
    ]);

    expect($module)->toBeInstanceOf(Module::class);
    expect($module->course_section_id)->toBe($section->id);
    expect($module->title)->toBe('Week 1: Introduction');
    expect($module->published_at)->toBeNull();

    $this->assertDatabaseHas('modules', [
        'title' => 'Week 1: Introduction',
        'course_section_id' => $section->id,
    ]);
});
