<?php

declare(strict_types=1);

use App\Domain\Audit\Actions\LogAction;
use App\Domain\Course\Actions\EnrollStudent;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Enums\UserRole;
use App\Models\User;

it('creates an enrollment record in a DB transaction', function () {
    $logAction = Mockery::mock(LogAction::class);
    $logAction->shouldReceive('execute')->once();

    $action = new EnrollStudent($logAction);

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'CS401',
        'title' => 'Enrollment Test Course',
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

    $enrollment = $action->execute($student, $section);

    expect($enrollment)->toBeInstanceOf(Enrollment::class);
    expect($enrollment->user_id)->toBe($student->id);
    expect($enrollment->course_section_id)->toBe($section->id);

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
    ]);
});

it('does not create duplicate enrollments when called twice without unique constraint handling', function () {
    $logAction = Mockery::mock(LogAction::class);
    $logAction->shouldReceive('execute')->zeroOrMoreTimes();

    $action = new EnrollStudent($logAction);

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'CS402',
        'title' => 'Enrollment Duplicate Test',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'B',
        'instructor_id' => $instructor->id,
    ]);

    $enrollment = $action->execute($student, $section);

    expect($enrollment)->toBeInstanceOf(Enrollment::class);
    $this->assertDatabaseCount('enrollments', 1);
});
