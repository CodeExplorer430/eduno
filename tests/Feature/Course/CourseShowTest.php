<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use App\Enums\UserRole;
use App\Models\User;

// ─── Helpers ─────────────────────────────────────────────────────────────────

function makeCourseForShowTest(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'CS'.fake()->unique()->numberBetween(200, 999),
        'title' => 'Show Test Course',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
        'status' => 'draft',
        'created_by' => $instructor->id,
    ]);

    return [$instructor, $course];
}

// ─── Tests ────────────────────────────────────────────────────────────────────

test('admin can view any course show page', function (): void {
    [, $course] = makeCourseForShowTest();
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->get(route('courses.show', $course))
        ->assertOk();
});

test('student is forbidden from courses.edit', function (): void {
    [, $course] = makeCourseForShowTest();
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->get(route('courses.edit', $course))
        ->assertForbidden();
});
