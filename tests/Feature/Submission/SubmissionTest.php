<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Submission\Models\Submission;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// ─── Helpers ─────────────────────────────────────────────────────────────────

function makeSubmissionSetup(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'SUB'.fake()->unique()->numberBetween(100, 999),
        'title' => 'Test Course',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);
    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'Section A',
        'instructor_id' => $instructor->id,
    ]);
    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Test Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => now()->subMinute(),
    ]);

    return [$instructor, $section, $assignment];
}

function enrollStudentForSubmission(User $student, CourseSection $section): void
{
    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);
}

// ─── Student submit ───────────────────────────────────────────────────────────

test('enrolled student can submit files to a published assignment', function (): void {
    Storage::fake('private');
    [$instructor, $section, $assignment] = makeSubmissionSetup();
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentForSubmission($student, $section);
    $file = UploadedFile::fake()->create('essay.pdf', 512, 'application/pdf');

    $this->actingAs($student)
        ->post(route('assignments.submissions.store', $assignment), [
            'files' => [$file],
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('submissions', [
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
    ]);
});

test('unenrolled student cannot submit', function (): void {
    Storage::fake('private');
    [, , $assignment] = makeSubmissionSetup();
    $student = User::factory()->create(['role' => UserRole::Student]);
    $file = UploadedFile::fake()->create('essay.pdf', 512, 'application/pdf');

    $this->actingAs($student)
        ->post(route('assignments.submissions.store', $assignment), [
            'files' => [$file],
        ])
        ->assertForbidden();
});

test('student cannot submit to unpublished assignment', function (): void {
    Storage::fake('private');
    [$instructor, $section] = makeSubmissionSetup();
    $unpublished = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Draft Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => null,
    ]);
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentForSubmission($student, $section);
    $file = UploadedFile::fake()->create('essay.pdf', 512, 'application/pdf');

    $this->actingAs($student)
        ->post(route('assignments.submissions.store', $unpublished), [
            'files' => [$file],
        ])
        ->assertForbidden();
});

test('late submission is flagged correctly', function (): void {
    Storage::fake('private');
    [$instructor, $section] = makeSubmissionSetup();
    $lateAssignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Late Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => now()->subDay(),
        'due_at' => now()->subHour(),
    ]);
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentForSubmission($student, $section);
    $file = UploadedFile::fake()->create('essay.pdf', 512, 'application/pdf');

    $this->actingAs($student)
        ->post(route('assignments.submissions.store', $lateAssignment), [
            'files' => [$file],
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('submissions', [
        'assignment_id' => $lateAssignment->id,
        'student_id' => $student->id,
        'is_late' => true,
    ]);
});

test('file type validation rejects invalid MIME', function (): void {
    Storage::fake('private');
    [$instructor, $section, $assignment] = makeSubmissionSetup();
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentForSubmission($student, $section);
    $file = UploadedFile::fake()->create('virus.exe', 512, 'application/x-msdownload');

    $this->actingAs($student)
        ->post(route('assignments.submissions.store', $assignment), [
            'files' => [$file],
        ])
        ->assertSessionHasErrors(['files.0']);
});

test('student cannot submit twice when allow_resubmission is false', function (): void {
    Storage::fake('private');
    [$instructor, $section, $assignment] = makeSubmissionSetup();
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentForSubmission($student, $section);

    Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => SubmissionStatus::Submitted,
        'submitted_at' => now(),
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    $file = UploadedFile::fake()->create('essay.pdf', 512, 'application/pdf');

    $this->actingAs($student)
        ->post(route('assignments.submissions.store', $assignment), [
            'files' => [$file],
        ])
        ->assertForbidden();
});

// ─── Instructor view ─────────────────────────────────────────────────────────

test('instructor can view all submissions for an assignment', function (): void {
    Storage::fake('private');
    [$instructor, $section, $assignment] = makeSubmissionSetup();
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentForSubmission($student, $section);

    Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => SubmissionStatus::Submitted,
        'submitted_at' => now(),
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    $this->actingAs($instructor)
        ->get(route('assignments.submissions.index', $assignment))
        ->assertOk();
});

test('student cannot access the instructor submissions index', function (): void {
    [$instructor, $section, $assignment] = makeSubmissionSetup();
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentForSubmission($student, $section);

    $this->actingAs($student)
        ->get(route('assignments.submissions.index', $assignment))
        ->assertForbidden();
});

test('student can view only their own submission', function (): void {
    Storage::fake('private');
    [$instructor, $section, $assignment] = makeSubmissionSetup();
    $student = User::factory()->create(['role' => UserRole::Student]);
    $otherStudent = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentForSubmission($student, $section);
    enrollStudentForSubmission($otherStudent, $section);

    $mySubmission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => SubmissionStatus::Submitted,
        'submitted_at' => now(),
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    $otherSubmission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $otherStudent->id,
        'status' => SubmissionStatus::Submitted,
        'submitted_at' => now(),
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    $this->actingAs($student)
        ->get(route('submissions.show', $mySubmission))
        ->assertOk();

    $this->actingAs($student)
        ->get(route('submissions.show', $otherSubmission))
        ->assertForbidden();
});
