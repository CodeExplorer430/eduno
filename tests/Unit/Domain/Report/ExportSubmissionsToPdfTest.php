<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Report\Actions\ExportSubmissionsToPdf;
use App\Domain\Submission\Models\Submission;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

uses(RefreshDatabase::class);

test('response is an HTTP response with pdf content-type', function (): void {
    $response = (new ExportSubmissionsToPdf())(collect());

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->headers->get('Content-Type'))->toContain('application/pdf');
});

test('response has content-disposition attachment with submissions filename', function (): void {
    $response = (new ExportSubmissionsToPdf())(collect());

    expect($response->headers->get('Content-Disposition'))->toContain('submissions.pdf');
});

test('generates PDF from submissions with student and assignment data', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student    = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code'          => 'PDF' . random_int(100, 999),
        'title'         => 'PDF Test Course',
        'department'    => 'CS',
        'term'          => '1st',
        'academic_year' => '2025-2026',
        'status'        => 'published',
        'created_by'    => $instructor->id,
    ]);

    $section = CourseSection::create([
        'course_id'     => $course->id,
        'section_name'  => 'A',
        'instructor_id' => $instructor->id,
    ]);

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title'             => 'Final Project',
        'max_score'         => 100,
    ]);

    $submission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id'    => $student->id,
        'status'        => 'submitted',
        'submitted_at'  => now(),
        'is_late'       => false,
        'attempt_no'    => 1,
    ]);

    $collection = Submission::with(['student', 'assignment', 'grade'])
        ->where('id', $submission->id)
        ->get();

    $response = (new ExportSubmissionsToPdf())($collection);

    expect($response->headers->get('Content-Type'))->toContain('application/pdf');
});
