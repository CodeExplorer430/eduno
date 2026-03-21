<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Enums\UserRole;
use App\Models\User;

it('instructor can view assignments for their section', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'ASN101',
        'title' => 'Assignment View Course',
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

    Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Midterm Paper',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    $response = $this->actingAs($instructor)->get(
        route('instructor.courses.assignments.index', $section)
    );

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
        ->component('Instructor/Assignments/Index')
        ->has('assignments')
    );
});

it('instructor can create an assignment', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'ASN102',
        'title' => 'Assignment Create Course',
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

    $response = $this->actingAs($instructor)->post(
        route('instructor.courses.assignments.store', $section),
        [
            'title' => 'Final Report',
            'instructions' => 'Write a 10-page report.',
            'max_score' => 100,
            'allow_resubmission' => false,
        ]
    );

    $response->assertRedirect(route('instructor.courses.index'));
    $this->assertDatabaseHas('assignments', [
        'title' => 'Final Report',
        'course_section_id' => $section->id,
    ]);
});

it('instructor can update their assignment', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'ASN103',
        'title' => 'Assignment Update Course',
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
        'title' => 'Original Title',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    $response = $this->actingAs($instructor)->patch(
        route('instructor.assignments.update', $assignment),
        [
            'title' => 'Updated Title',
            'max_score' => 100,
            'allow_resubmission' => false,
        ]
    );

    $response->assertRedirect(route('instructor.courses.index'));
    $this->assertDatabaseHas('assignments', [
        'id' => $assignment->id,
        'title' => 'Updated Title',
    ]);
});

it('instructor can delete their assignment', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'ASN104',
        'title' => 'Assignment Delete Course',
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
        'title' => 'To Be Deleted',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    $response = $this->actingAs($instructor)->delete(
        route('instructor.assignments.destroy', $assignment)
    );

    $response->assertRedirect(route('instructor.courses.index'));
    $this->assertDatabaseMissing('assignments', ['id' => $assignment->id]);
});

it('non-owner instructor cannot update assignment', function () {
    $ownerInstructor = User::factory()->create(['role' => UserRole::Instructor]);
    $otherInstructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'ASN105',
        'title' => 'Ownership Test Course',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $ownerInstructor->id,
    ]);

    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'A',
        'instructor_id' => $ownerInstructor->id,
    ]);

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Protected Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    $response = $this->actingAs($otherInstructor)->patch(
        route('instructor.assignments.update', $assignment),
        ['title' => 'Hijacked', 'max_score' => 100, 'allow_resubmission' => false]
    );

    $response->assertStatus(403);
});

it('student cannot create an assignment', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'ASN106',
        'title' => 'Student Block Course',
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

    $response = $this->actingAs($student)->post(
        route('instructor.courses.assignments.store', $section),
        ['title' => 'Injected Assignment', 'max_score' => 100, 'allow_resubmission' => false]
    );

    $response->assertStatus(403);
});
