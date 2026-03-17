<?php

declare(strict_types=1);

use App\Domain\Assignment\Actions\CreateAssignment;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;

it('creates an Assignment record with the correct course_section_id', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'ASN201',
        'title' => 'Create Assignment Action Test',
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

    $action = new CreateAssignment;

    $assignment = $action->execute($section, [
        'title' => 'Final Project',
        'instructions' => 'Build a web app.',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    expect($assignment)->toBeInstanceOf(Assignment::class);
    expect($assignment->course_section_id)->toBe($section->id);
    expect($assignment->title)->toBe('Final Project');
    expect($assignment->max_score)->toEqual(100);

    $this->assertDatabaseHas('assignments', [
        'title' => 'Final Project',
        'course_section_id' => $section->id,
    ]);
});
