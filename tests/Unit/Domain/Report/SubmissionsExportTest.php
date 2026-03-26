<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Report\Actions\SubmissionsExport;
use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeSubmissionsExportFixture(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student    = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code'          => 'SXE' . fake()->unique()->numberBetween(100, 999),
        'title'         => 'Export Test Course',
        'department'    => 'CCS',
        'term'          => '1st Semester',
        'academic_year' => '2025-2026',
        'status'        => 'published',
        'created_by'    => $instructor->id,
    ]);

    $section = CourseSection::create([
        'course_id'    => $course->id,
        'section_name' => 'A',
        'instructor_id' => $instructor->id,
    ]);

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title'             => 'Test Assignment',
        'max_score'         => 100,
        'allow_resubmission' => false,
    ]);

    $submission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id'    => $student->id,
        'status'        => 'submitted',
        'is_late'       => false,
        'attempt_no'    => 1,
        'submitted_at'  => now(),
    ]);

    return [$instructor, $student, $assignment, $submission];
}

test('headings returns 7 column headers', function (): void {
    $export = new SubmissionsExport(collect());

    expect($export->headings())->toBe([
        'ID', 'Student', 'Assignment', 'Submitted At', 'Is Late', 'Score', 'Released',
    ]);
});

test('map returns correct row structure for a graded submission', function (): void {
    [$instructor, $student, $assignment, $submission] = makeSubmissionsExportFixture();

    Grade::create([
        'submission_id' => $submission->id,
        'graded_by'     => $instructor->id,
        'score'         => 90.0,
        'released_at'   => now(),
    ]);

    $submission->load(['student', 'assignment', 'grade']);

    $export = new SubmissionsExport(collect([$submission]));
    $row    = $export->map($submission);

    expect($row[0])->toBe($submission->id);
    expect($row[1])->toBe($student->name);
    expect($row[2])->toBe($assignment->title);
    expect($row[4])->toBe('No');
    expect($row[5])->toBe('90.00');
});

test('map returns empty score and released when grade is null', function (): void {
    [, , , $submission] = makeSubmissionsExportFixture();
    $submission->load(['student', 'assignment', 'grade']);

    $export = new SubmissionsExport(collect([$submission]));
    $row    = $export->map($submission);

    expect($row[5])->toBe('');
    expect($row[6])->toBe('');
});

test('map sanitizes student name starting with equals sign by prepending a tab', function (): void {
    [, $student, , $submission] = makeSubmissionsExportFixture();
    $student->name = '=HYPERLINK("http://evil.com")';

    $submission->setRelation('student', $student);
    $submission->load(['assignment', 'grade']);

    $export = new SubmissionsExport(collect([$submission]));
    $row    = $export->map($submission);

    expect($row[1])->toStartWith("\t");
    expect($row[1])->toContain('=HYPERLINK');
});

test('map sanitizes assignment title starting with plus sign by prepending a tab', function (): void {
    [, , $assignment, $submission] = makeSubmissionsExportFixture();
    $assignment->title = '+cmd|"/c calc"!A1';

    $submission->setRelation('assignment', $assignment);
    $submission->load(['student', 'grade']);

    $export = new SubmissionsExport(collect([$submission]));
    $row    = $export->map($submission);

    expect($row[2])->toStartWith("\t");
});

test('styles applies bold white header on dark blue background', function (): void {
    $export = new SubmissionsExport(collect());

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet       = $spreadsheet->getActiveSheet();
    $export->styles($sheet);

    $style = $sheet->getStyle('A1:G1');
    expect($style->getFont()->getBold())->toBeTrue();
    expect(strtoupper($style->getFill()->getStartColor()->getRGB()))->toBe('1E3A5F');
});
