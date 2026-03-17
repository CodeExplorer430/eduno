<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Grade\Models\Grade;
use App\Domain\Report\Actions\ExportSubmissionsAsCsv;
use App\Domain\Submission\Models\Submission;
use App\Enums\UserRole;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

it('returns a StreamedResponse with CSV content-type headers', function () {
    $action = new ExportSubmissionsAsCsv;

    $response = $action->__invoke();

    expect($response)->toBeInstanceOf(StreamedResponse::class);
    expect($response->headers->get('Content-Type'))->toContain('text/csv');
    expect($response->headers->get('Content-Disposition'))->toContain('submissions-export.csv');
});

it('includes submission data in the CSV output', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'RPT101',
        'title' => 'Export CSV Test',
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
        'title' => 'CSV Export Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    $submission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => 'submitted',
        'is_late' => false,
        'attempt_no' => 1,
        'submitted_at' => now(),
    ]);

    Grade::create([
        'submission_id' => $submission->id,
        'graded_by' => $instructor->id,
        'score' => 88.00,
        'released_at' => now(),
    ]);

    $action = new ExportSubmissionsAsCsv;
    $response = $action->__invoke();

    ob_start();
    $response->sendContent();
    $output = ob_get_clean();

    expect($output)->toContain('CSV Export Assignment');
    expect($output)->toContain($student->name);
    expect($output)->toContain('88');
});
