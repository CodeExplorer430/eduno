<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Grade\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Enums\UserRole;
use App\Models\User;

it('student can view their released grades', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'GRD101',
        'title' => 'Grade View Course',
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
        'title' => 'Graded Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    $submission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => 'submitted',
        'submitted_at' => now(),
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    Grade::create([
        'submission_id' => $submission->id,
        'graded_by' => $instructor->id,
        'score' => 88,
        'released_at' => now(),
    ]);

    $response = $this->actingAs($student)->get(route('student.grades.index'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
        ->component('Student/Grades/Index')
        ->has('grades')
    );
});

it('only released grades are returned to the student', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'GRD102',
        'title' => 'Grade Filter Course',
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

    $assignmentA = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Released Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    $assignmentB = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Unreleased Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    $submissionA = Submission::create([
        'assignment_id' => $assignmentA->id,
        'student_id' => $student->id,
        'status' => 'submitted',
        'submitted_at' => now(),
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    $submissionB = Submission::create([
        'assignment_id' => $assignmentB->id,
        'student_id' => $student->id,
        'status' => 'submitted',
        'submitted_at' => now(),
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    Grade::create([
        'submission_id' => $submissionA->id,
        'graded_by' => $instructor->id,
        'score' => 90,
        'released_at' => now(),
    ]);

    Grade::create([
        'submission_id' => $submissionB->id,
        'graded_by' => $instructor->id,
        'score' => 75,
        'released_at' => null,
    ]);

    $response = $this->actingAs($student)->get(route('student.grades.index'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
        ->component('Student/Grades/Index')
        ->has('grades', 1)
    );
});
