<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Audit\Actions\LogAction;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Grade\Actions\ReleaseGrade;
use App\Domain\Grade\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Enums\UserRole;
use App\Jobs\NotifyStudentGradeReleased;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

it('sets released_at timestamp on the grade', function () {
    Queue::fake();

    $logAction = Mockery::mock(LogAction::class);
    $logAction->shouldReceive('execute')->once();

    $action = new ReleaseGrade($logAction);

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'CS601',
        'title' => 'Release Grade Test',
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
        'title' => 'Graded Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    $submission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => 'submitted',
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    $grade = Grade::create([
        'submission_id' => $submission->id,
        'graded_by' => $instructor->id,
        'score' => 85.00,
        'feedback' => 'Good work!',
        'released_at' => null,
    ]);

    $result = $action->execute($instructor, $grade);

    expect($result->released_at)->not->toBeNull();
    $this->assertDatabaseHas('grades', [
        'id' => $grade->id,
    ]);
});

it('dispatches NotifyStudentGradeReleased job when grade is released', function () {
    Queue::fake();

    $logAction = Mockery::mock(LogAction::class);
    $logAction->shouldReceive('execute')->once();

    $action = new ReleaseGrade($logAction);

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'CS602',
        'title' => 'Job Dispatch Test',
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
        'title' => 'Job Test Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    $submission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => 'submitted',
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    $grade = Grade::create([
        'submission_id' => $submission->id,
        'graded_by' => $instructor->id,
        'score' => 90.00,
        'feedback' => null,
        'released_at' => null,
    ]);

    $action->execute($instructor, $grade);

    Queue::assertPushed(NotifyStudentGradeReleased::class);
});
