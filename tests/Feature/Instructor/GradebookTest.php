<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─── Fixtures ─────────────────────────────────────────────────────────────────

function makeGradebookSection(User $instructor): CourseSection
{
    $course = Course::create([
        'code'          => 'GBK'.fake()->unique()->numberBetween(100, 999),
        'title'         => 'Gradebook Test Course',
        'department'    => 'CCS',
        'term'          => '1st Semester',
        'academic_year' => '2025-2026',
        'status'        => 'published',
        'created_by'    => $instructor->id,
    ]);

    return CourseSection::create([
        'course_id'     => $course->id,
        'section_name'  => 'A',
        'instructor_id' => $instructor->id,
    ]);
}

function enrollForGradebook(User $student, CourseSection $section): void
{
    Enrollment::create([
        'user_id'          => $student->id,
        'course_section_id' => $section->id,
        'status'           => 'active',
        'enrolled_at'      => now(),
    ]);
}

function makeGradedSubmission(
    User $instructor,
    User $student,
    Assignment $assignment,
    float $score,
    bool $released = true,
    int $attempt = 1
): Submission {
    $submission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id'    => $student->id,
        'status'        => SubmissionStatus::Graded,
        'submitted_at'  => now(),
        'is_late'       => false,
        'attempt_no'    => $attempt,
    ]);

    Grade::create([
        'submission_id' => $submission->id,
        'graded_by'     => $instructor->id,
        'score'         => $score,
        'released_at'   => $released ? now() : null,
    ]);

    return $submission;
}

// ─── Tests ────────────────────────────────────────────────────────────────────

test('instructor can view their own section gradebook', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $section    = makeGradebookSection($instructor);

    $this->actingAs($instructor)
        ->get(route('instructor.gradebook.show', $section))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Instructor/Gradebook/Index'));
});

test('instructor cannot view another instructors section gradebook', function (): void {
    $owner  = User::factory()->create(['role' => UserRole::Instructor]);
    $other  = User::factory()->create(['role' => UserRole::Instructor]);
    $section = makeGradebookSection($owner);

    $this->actingAs($other)
        ->get(route('instructor.gradebook.show', $section))
        ->assertForbidden();
});

test('admin can view any section gradebook', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $admin      = User::factory()->create(['role' => UserRole::Admin]);
    $section    = makeGradebookSection($instructor);

    $this->actingAs($admin)
        ->get(route('instructor.gradebook.show', $section))
        ->assertOk();
});

test('guest is redirected to login from gradebook', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $section    = makeGradebookSection($instructor);

    $this->get(route('instructor.gradebook.show', $section))
        ->assertRedirect(route('login'));
});

test('gradebook has required inertia props', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $section    = makeGradebookSection($instructor);

    $this->actingAs($instructor)
        ->get(route('instructor.gradebook.show', $section))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
            ->component('Instructor/Gradebook/Index')
            ->has('section')
            ->has('assignments')
            ->has('students')
            ->has('averages')
        );
});

test('gradebook export returns csv with correct headers', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $section    = makeGradebookSection($instructor);

    Assignment::create([
        'course_section_id'  => $section->id,
        'title'              => 'HW 1',
        'max_score'          => 100,
        'allow_resubmission' => false,
        'published_at'       => now()->subMinute(),
        'due_at'             => now()->addDays(7),
    ]);

    $response = $this->actingAs($instructor)
        ->get(route('instructor.gradebook.export', $section));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

    $content = $response->streamedContent();
    expect($content)->toContain('Student');
    expect($content)->toContain('HW 1');
    expect($content)->toContain('Total');
});

test('gradebook export csv includes student row', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student    = User::factory()->create(['role' => UserRole::Student]);
    $section    = makeGradebookSection($instructor);
    enrollForGradebook($student, $section);

    $assignment = Assignment::create([
        'course_section_id'  => $section->id,
        'title'              => 'Quiz 1',
        'max_score'          => 50,
        'allow_resubmission' => false,
        'published_at'       => now()->subMinute(),
        'due_at'             => now()->addDays(3),
    ]);

    makeGradedSubmission($instructor, $student, $assignment, 45.0);

    $content = $this->actingAs($instructor)
        ->get(route('instructor.gradebook.export', $section))
        ->streamedContent();

    expect($content)->toContain($student->name);
    expect($content)->toContain('45');
});
