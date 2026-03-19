<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Report\Actions\ExportSubmissions;
use App\Domain\Submission\Models\Submission;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\User;

function makeExportActionSetup(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'RPT'.fake()->unique()->numberBetween(100, 999),
        'title' => 'Report Course',
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
        'title' => 'Report Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => now()->subMinute(),
    ]);

    return [$instructor, $section, $assignment];
}

test('export returns string with csv header row', function (): void {
    [, , $assignment] = makeExportActionSetup();

    $csv = (new ExportSubmissions)->handle($assignment);

    expect($csv)->toContain('student_name,student_email,submitted_at,is_late,attempt_no,score,feedback');
});

test('export includes all submissions for the assignment', function (): void {
    [$instructor, $section, $assignment] = makeExportActionSetup();

    $student = User::factory()->create(['role' => UserRole::Student]);
    Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => SubmissionStatus::Submitted,
        'submitted_at' => now(),
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    $csv = (new ExportSubmissions)->handle($assignment);
    $lines = array_filter(explode("\n", trim($csv)));

    expect(count($lines))->toBe(2); // header + 1 submission
    expect($csv)->toContain($student->email);
});
