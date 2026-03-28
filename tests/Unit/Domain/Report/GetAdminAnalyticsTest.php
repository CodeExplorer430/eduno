<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Report\Actions\GetAdminAnalytics;
use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

function makeAnalyticsSection(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student    = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code'          => 'ANLT' . random_int(100, 999),
        'title'         => 'Analytics Test Course',
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
        'title'             => 'Analytics Assignment',
        'max_score'         => 100,
    ]);

    return [$instructor, $student, $course, $section, $assignment];
}

test('submissionTrend returns 8 data points (weeks)', function (): void {
    $result = (new GetAdminAnalytics())->handle();
    $trend  = $result['submission_trend'];

    expect(count($trend['labels']))->toBe(8);
    expect(count($trend['onTime']))->toBe(8);
    expect(count($trend['late']))->toBe(8);
});

test('gradeDistribution returns 6 score band labels', function (): void {
    $result = (new GetAdminAnalytics())->handle();
    $dist   = $result['grade_distribution'];

    expect($dist['labels'])->toBe(['0–49', '50–59', '60–69', '70–79', '80–89', '90–100']);
    expect(count($dist['counts']))->toBe(6);
});

test('gradeDistribution correctly counts grades into bands', function (): void {
    [, , , , $assignment] = makeAnalyticsSection();
    $student = User::factory()->create(['role' => UserRole::Student]);
    $grader  = User::factory()->create(['role' => UserRole::Instructor]);

    $submission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id'    => $student->id,
        'status'        => SubmissionStatus::Graded,
        'submitted_at'  => now(),
        'is_late'       => false,
        'attempt_no'    => 1,
    ]);

    Grade::create([
        'submission_id' => $submission->id,
        'graded_by'     => $grader->id,
        'score'         => 95,
        'released_at'   => now(),
    ]);

    Cache::flush();

    $result = (new GetAdminAnalytics())->handle();
    $dist   = $result['grade_distribution'];

    // 95 falls in the 90–100 band (index 5)
    expect($dist['counts'][5])->toBe(1);
});

test('lateRateByCourse returns labels and rates arrays', function (): void {
    [, $student, $course, , $assignment] = makeAnalyticsSection();

    Submission::create([
        'assignment_id' => $assignment->id,
        'student_id'    => $student->id,
        'status'        => SubmissionStatus::Submitted,
        'submitted_at'  => now(),
        'is_late'       => true,
        'attempt_no'    => 1,
    ]);

    Submission::create([
        'assignment_id' => $assignment->id,
        'student_id'    => User::factory()->create(['role' => UserRole::Student])->id,
        'status'        => SubmissionStatus::Submitted,
        'submitted_at'  => now(),
        'is_late'       => false,
        'attempt_no'    => 1,
    ]);

    Cache::flush();

    $result = (new GetAdminAnalytics())->handle();
    $rates  = $result['late_rate_by_course'];

    expect($rates)->toHaveKeys(['labels', 'rates']);
    $idx = array_search($course->code, $rates['labels']);
    expect($idx)->not->toBeFalse();
    expect($rates['rates'][$idx])->toBe(50.0);
});

test('statusBreakdown returns labels and counts arrays', function (): void {
    [, $student, , , $assignment] = makeAnalyticsSection();

    Submission::create([
        'assignment_id' => $assignment->id,
        'student_id'    => $student->id,
        'status'        => SubmissionStatus::Submitted,
        'submitted_at'  => now(),
        'is_late'       => false,
        'attempt_no'    => 1,
    ]);

    Cache::flush();

    $result    = (new GetAdminAnalytics())->handle();
    $breakdown = $result['status_breakdown'];

    expect($breakdown)->toHaveKeys(['labels', 'counts']);
    expect($breakdown['labels'])->toContain(SubmissionStatus::Submitted->value);
});

test('analytics results are cached after first call', function (): void {
    Cache::flush();

    (new GetAdminAnalytics())->handle();

    expect(Cache::has('report.admin.analytics'))->toBeTrue();
});
