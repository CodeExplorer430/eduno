<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Submission\Models\Submission;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeAssignmentWithSubmission(User $instructor, User $student): Assignment
{
    $course = Course::create([
        'code' => 'EXP'.random_int(100, 999),
        'title' => 'Export Test Course',
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
        'title' => 'Midterm Assignment',
        'max_score' => 100,
    ]);

    Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => 'submitted',
        'submitted_at' => now(),
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    return $assignment;
}

it('instructor can export submissions as csv', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);
    $assignment = makeAssignmentWithSubmission($instructor, $student);

    $response = $this->actingAs($instructor)->get(
        route('instructor.submissions.export', $assignment)
    );

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

it('non-owner instructor cannot export submissions', function () {
    $ownerInstructor = User::factory()->create(['role' => UserRole::Instructor]);
    $otherInstructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);
    $assignment = makeAssignmentWithSubmission($ownerInstructor, $student);

    $response = $this->actingAs($otherInstructor)->get(
        route('instructor.submissions.export', $assignment)
    );

    $response->assertStatus(403);
});

it('student cannot access submission export', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);
    $assignment = makeAssignmentWithSubmission($instructor, $student);

    $response = $this->actingAs($student)->get(
        route('instructor.submissions.export', $assignment)
    );

    $response->assertStatus(403);
});

it('csv contains expected header row', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);
    $assignment = makeAssignmentWithSubmission($instructor, $student);

    $response = $this->actingAs($instructor)->get(
        route('instructor.submissions.export', $assignment)
    );

    $response->assertStatus(200);
    $content = $response->streamedContent();
    expect($content)->toContain('ID,Student,Assignment,"Submitted At","Is Late",Score,Released');
});
