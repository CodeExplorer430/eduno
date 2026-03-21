<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Enums\UserRole;
use App\Models\User;

it('enrolled student can view assignment list', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'SASN101',
        'title' => 'Student Assignment Course',
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

    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
    ]);

    Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Published Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => now()->subDay(),
    ]);

    $response = $this->actingAs($student)->get(route('student.assignments.index'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
        ->component('Student/Assignments/Index')
        ->has('assignments')
    );
});

it('enrolled student can view single assignment', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'SASN102',
        'title' => 'Student Assignment Show Course',
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

    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
    ]);

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Visible Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => now()->subDay(),
    ]);

    $response = $this->actingAs($student)->get(
        route('student.assignments.show', $assignment)
    );

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
        ->component('Student/Assignments/Show')
    );
});

it('non-enrolled student cannot view assignment', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $enrolledStudent = User::factory()->create(['role' => UserRole::Student]);
    $unenrolledStudent = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'SASN103',
        'title' => 'Enrollment Guard Course',
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

    Enrollment::create([
        'user_id' => $enrolledStudent->id,
        'course_section_id' => $section->id,
        'status' => 'active',
    ]);

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Protected Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => now()->subDay(),
    ]);

    $response = $this->actingAs($unenrolledStudent)->get(
        route('student.assignments.show', $assignment)
    );

    $response->assertStatus(403);
});
