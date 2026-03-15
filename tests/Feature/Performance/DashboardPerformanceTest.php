<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Maximum number of SQL queries allowed per dashboard request.
 * Acts as a regression guard against N+1 queries being introduced.
 */
const DASHBOARD_QUERY_BUDGET = 20;

/**
 * Create a minimal course section owned by the given instructor.
 */
function makeSectionForPerfTest(User $instructor): CourseSection
{
    $course = Course::create([
        'code' => 'PERF'.fake()->unique()->numberBetween(100, 999),
        'title' => 'Perf Test Course',
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

test('student dashboard runs within query budget', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);
    $section = makeSectionForPerfTest($instructor);

    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);

    DB::enableQueryLog();
    $this->actingAs($student)->get(route('dashboard'))->assertOk();
    $count = count(DB::getQueryLog());
    DB::disableQueryLog();

    expect($count)->toBeLessThanOrEqual(DASHBOARD_QUERY_BUDGET);
});

test('instructor dashboard runs within query budget', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    makeSectionForPerfTest($instructor);
    makeSectionForPerfTest($instructor);
    makeSectionForPerfTest($instructor);

    DB::enableQueryLog();
    $this->actingAs($instructor)->get(route('dashboard'))->assertOk();
    $count = count(DB::getQueryLog());
    DB::disableQueryLog();

    expect($count)->toBeLessThanOrEqual(DASHBOARD_QUERY_BUDGET);
});

test('admin dashboard runs within query budget', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    DB::enableQueryLog();
    $this->actingAs($admin)->get(route('dashboard'))->assertOk();
    $count = count(DB::getQueryLog());
    DB::disableQueryLog();

    expect($count)->toBeLessThanOrEqual(DASHBOARD_QUERY_BUDGET);
});
