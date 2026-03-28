<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Report\Actions\ExportSubmissionsToCsv;
use App\Domain\Submission\Models\Submission;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\StreamedResponse;

uses(RefreshDatabase::class);

function makeSubmissionsForCsvTest(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student    = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code'          => 'CSV' . random_int(100, 999),
        'title'         => 'CSV Test Course',
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
        'title'             => 'Midterm Exam',
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

    return [$submission, $student, $assignment];
}

function captureCsvSubmissions(StreamedResponse $response): string
{
    ob_start();
    $response->sendContent();

    return (string) ob_get_clean();
}

test('response is a StreamedResponse with text/csv content-type', function (): void {
    $response = (new ExportSubmissionsToCsv())(collect());

    expect($response)->toBeInstanceOf(StreamedResponse::class);
    expect($response->headers->get('Content-Type'))->toContain('text/csv');
});

test('response has content-disposition attachment with submissions filename', function (): void {
    $response = (new ExportSubmissionsToCsv())(collect());

    expect($response->headers->get('Content-Disposition'))->toContain('submissions.csv');
});

test('CSV output contains the expected header row', function (): void {
    $response  = (new ExportSubmissionsToCsv())(collect());
    $csv       = captureCsvSubmissions($response);
    $firstLine = explode("\n", $csv)[0];

    expect($firstLine)->toContain('ID');
    expect($firstLine)->toContain('Student');
    expect($firstLine)->toContain('Assignment');
    expect($firstLine)->toContain('Is Late');
    expect($firstLine)->toContain('Score');
});

test('CSV output contains one data row per submission', function (): void {
    [$sub1]  = makeSubmissionsForCsvTest();
    [$sub2]  = makeSubmissionsForCsvTest();

    $collection = Submission::with(['student', 'assignment', 'grade'])
        ->whereIn('id', [$sub1->id, $sub2->id])
        ->get();

    $response = (new ExportSubmissionsToCsv())($collection);
    $csv      = captureCsvSubmissions($response);
    $lines    = array_values(array_filter(explode("\n", trim($csv))));

    // 1 header + 2 data rows
    expect(count($lines))->toBe(3);
});

test('CSV includes student name in data row', function (): void {
    [$sub, $student] = makeSubmissionsForCsvTest();

    $collection = Submission::with(['student', 'assignment', 'grade'])
        ->where('id', $sub->id)
        ->get();

    $response = (new ExportSubmissionsToCsv())($collection);
    $csv      = captureCsvSubmissions($response);

    expect($csv)->toContain($student->name);
});
