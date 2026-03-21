<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Submission\Actions\SubmitAssignment;
use App\Domain\Submission\Models\Submission;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

function makeSubmitSetup(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'USS'.fake()->unique()->numberBetween(100, 999),
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
        'title' => 'Unit Test Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => now()->subMinute(),
    ]);
    $student = User::factory()->create(['role' => UserRole::Student]);
    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);

    return [$assignment, $student];
}

test('it creates a submission with correct attempt_no', function (): void {
    Storage::fake('private');
    [$assignment, $student] = makeSubmitSetup();
    $action = new SubmitAssignment();
    $file = UploadedFile::fake()->create('essay.pdf', 512, 'application/pdf');

    $submission = $action->handle($assignment, $student, [$file]);

    expect($submission->attempt_no)->toBe(1);
});

test('second submission increments attempt_no', function (): void {
    Storage::fake('private');
    [$assignment, $student] = makeSubmitSetup();
    $action = new SubmitAssignment();
    $file1 = UploadedFile::fake()->create('essay1.pdf', 512, 'application/pdf');
    $file2 = UploadedFile::fake()->create('essay2.pdf', 512, 'application/pdf');

    $first = $action->handle($assignment, $student, [$file1]);
    $second = $action->handle($assignment, $student, [$file2]);

    expect($first->attempt_no)->toBe(1)
        ->and($second->attempt_no)->toBe(2);
});

test('it sets is_late true when assignment is past due', function (): void {
    Storage::fake('private');
    [$assignment, $student] = makeSubmitSetup();
    $assignment->update(['due_at' => now()->subHour()]);
    $action = new SubmitAssignment();
    $file = UploadedFile::fake()->create('essay.pdf', 512, 'application/pdf');

    $submission = $action->handle($assignment, $student, [$file]);

    expect($submission->is_late)->toBeTrue();
});

test('it sets is_late false when assignment is not past due', function (): void {
    Storage::fake('private');
    [$assignment, $student] = makeSubmitSetup();
    $assignment->update(['due_at' => now()->addDay()]);
    $action = new SubmitAssignment();
    $file = UploadedFile::fake()->create('essay.pdf', 512, 'application/pdf');

    $submission = $action->handle($assignment, $student, [$file]);

    expect($submission->is_late)->toBeFalse();
});

test('it stores files on private disk with UUID filenames', function (): void {
    Storage::fake('private');
    [$assignment, $student] = makeSubmitSetup();
    $action = new SubmitAssignment();
    $file = UploadedFile::fake()->create('essay.pdf', 512, 'application/pdf');

    $submission = $action->handle($assignment, $student, [$file]);

    expect($submission->files)->toHaveCount(1);
    $storedPath = $submission->files->first()->file_path;
    Storage::disk('private')->assertExists($storedPath);
});

test('it sets status to submitted', function (): void {
    Storage::fake('private');
    [$assignment, $student] = makeSubmitSetup();
    $action = new SubmitAssignment();
    $file = UploadedFile::fake()->create('essay.pdf', 512, 'application/pdf');

    $submission = $action->handle($assignment, $student, [$file]);

    expect($submission->status)->toBe(SubmissionStatus::Submitted);
});

test('it does not crash and skips notification when section has no instructor', function (): void {
    Notification::fake();
    Storage::fake('private');
    [$assignment, $student] = makeSubmitSetup();

    // Add a global scope that makes all User queries return empty results, simulating
    // a section whose instructor record has been removed. Applied after setup so that
    // factory creates still work normally.
    User::addGlobalScope('null_instructor_test', fn ($q) => $q->whereNull('id'));

    $action = new SubmitAssignment();
    $file = UploadedFile::fake()->create('essay.pdf', 512, 'application/pdf');

    $submission = $action->handle($assignment, $student, [$file]);

    expect($submission)->toBeInstanceOf(Submission::class);
    Notification::assertNothingSent();

    // Cleanup: remove the global scope so subsequent tests can query users normally.
    $reflection = new ReflectionClass(User::class);
    $property = $reflection->getProperty('globalScopes');
    $property->setAccessible(true);
    $scopes = $property->getValue(null);
    unset($scopes[User::class]['null_instructor_test']);
    $property->setValue(null, $scopes);
});
