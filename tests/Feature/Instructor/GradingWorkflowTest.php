<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Grade\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Enums\UserRole;
use App\Models\User;

it('instructor can access their courses list', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $response = $this->actingAs($instructor)->get(route('instructor.courses.index'));

    $response->assertStatus(200);
});

it('student cannot access instructor courses', function () {
    $student = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($student)->get(route('instructor.courses.index'));

    $response->assertStatus(403);
});

it('instructor can grade a submission', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'GRD101',
        'title' => 'Grading Test Course',
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
        'title' => 'Test Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    $submission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => 'submitted',
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    $response = $this->actingAs($instructor)->post(
        route('instructor.grades.store', $submission),
        ['score' => 85, 'feedback' => 'Good work!']
    );

    $response->assertRedirect();
    $this->assertDatabaseHas('grades', [
        'submission_id' => $submission->id,
        'score' => 85,
    ]);
});

it('instructor can release a grade', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'GRD102',
        'title' => 'Release Grade Course',
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
        'title' => 'Release Test Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    $submission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => 'submitted',
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    $grade = Grade::create([
        'submission_id' => $submission->id,
        'graded_by' => $instructor->id,
        'score' => 90.00,
        'feedback' => null,
        'released_at' => null,
    ]);

    $response = $this->actingAs($instructor)->patch(
        route('instructor.grades.release', $grade)
    );

    $response->assertRedirect();
    $this->assertDatabaseHas('grades', [
        'id' => $grade->id,
        'released_at' => now()->toDateTimeString(),
    ]);
});

it('score cannot exceed assignment max_score', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'GRD103',
        'title' => 'Max Score Validation Course',
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
        'title' => 'Validation Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    $submission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => 'submitted',
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    $response = $this->actingAs($instructor)->post(
        route('instructor.grades.store', $submission),
        ['score' => 999, 'feedback' => null]
    );

    $response->assertSessionHasErrors('score');
    $this->assertDatabaseMissing('grades', ['submission_id' => $submission->id]);
});

it('non-owner instructor cannot grade another sections submission', function () {
    $ownerInstructor = User::factory()->create(['role' => UserRole::Instructor]);
    $otherInstructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'GRD104',
        'title' => 'Ownership Grade Course',
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

    $submission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => 'submitted',
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    $response = $this->actingAs($otherInstructor)->post(
        route('instructor.grades.store', $submission),
        ['score' => 75, 'feedback' => null]
    );

    $response->assertStatus(403);
});
