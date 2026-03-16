<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Submission\Actions\SubmitAssignment;
use App\Domain\Submission\Models\Submission;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('private');
});

it('creates a Submission and SubmissionFile in a DB transaction', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'CS501',
        'title' => 'Submit Action Test Course',
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
        'title' => 'Submit Test',
        'max_score' => 100,
        'allow_resubmission' => false,
        'due_at' => null,
    ]);

    $file = UploadedFile::fake()->create('essay.pdf', 500, 'application/pdf');

    $action = new SubmitAssignment;
    $submission = $action->execute($student, $assignment, [$file]);

    expect($submission)->toBeInstanceOf(Submission::class);
    expect($submission->student_id)->toBe($student->id);
    expect($submission->assignment_id)->toBe($assignment->id);
    expect($submission->is_late)->toBeFalse();

    $this->assertDatabaseHas('submissions', [
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => 'submitted',
    ]);

    $this->assertDatabaseHas('submission_files', [
        'submission_id' => $submission->id,
        'original_name' => 'essay.pdf',
    ]);
});

it('sets is_late to true when submission is past due date', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'CS502',
        'title' => 'Late Submission Test',
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

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Late Test Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'due_at' => now()->subDay(),
    ]);

    $file = UploadedFile::fake()->create('late.pdf', 500, 'application/pdf');

    $action = new SubmitAssignment;
    $submission = $action->execute($student, $assignment, [$file]);

    expect($submission->is_late)->toBeTrue();
});

it('increments attempt_no on resubmission', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'CS503',
        'title' => 'Resubmission Test',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'C',
        'instructor_id' => $instructor->id,
    ]);

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Resubmit Assignment',
        'max_score' => 100,
        'allow_resubmission' => true,
        'due_at' => null,
    ]);

    $file1 = UploadedFile::fake()->create('first.pdf', 500, 'application/pdf');
    $file2 = UploadedFile::fake()->create('second.pdf', 500, 'application/pdf');

    $action = new SubmitAssignment;

    $first = $action->execute($student, $assignment, [$file1]);
    $second = $action->execute($student, $assignment, [$file2]);

    expect($first->attempt_no)->toBe(1);
    expect($second->attempt_no)->toBe(2);
});
