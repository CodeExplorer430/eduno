<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Submission\Models\Submission;
use App\Enums\UserRole;
use App\Models\User;

it('admin can view reports dashboard', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('admin.reports.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Admin/Reports/Index')
        ->has('stats')
    );
});

it('non-admin cannot access reports dashboard', function () {
    $student = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($student)->get(route('admin.reports.index'));

    $response->assertStatus(403);
});

it('admin can export all submissions as csv', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'RPT101',
        'title' => 'Report Test Course',
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

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Export Test Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => 'submitted',
        'submitted_at' => now(),
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.reports.export'));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

it('non-admin instructor cannot export report csv', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $response = $this->actingAs($instructor)->get(route('admin.reports.export'));

    $response->assertStatus(403);
});
