<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Enums\UserRole;
use App\Models\User;

// ─── Helpers ─────────────────────────────────────────────────────────────────

function makeAssignmentSection(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'ASN'.fake()->unique()->numberBetween(100, 999),
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

    return [$instructor, $section];
}

function makeAssignment(CourseSection $section, bool $published = false): Assignment
{
    return Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Test Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
        'published_at' => $published ? now()->subMinute() : null,
    ]);
}

function enrollStudentForAssignment(User $student, CourseSection $section): void
{
    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);
}

// ─── CRUD ─────────────────────────────────────────────────────────────────────

test('instructor can create an assignment', function (): void {
    [$instructor, $section] = makeAssignmentSection();

    $this->actingAs($instructor)
        ->post(route('sections.assignments.store', $section), [
            'title' => 'Midterm Exam',
            'max_score' => 100,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('assignments', [
        'course_section_id' => $section->id,
        'title' => 'Midterm Exam',
    ]);
});

test('student cannot create an assignment', function (): void {
    [, $section] = makeAssignmentSection();
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->post(route('sections.assignments.store', $section), ['title' => 'Hack'])
        ->assertForbidden();
});

test('instructor can update their assignment', function (): void {
    [$instructor, $section] = makeAssignmentSection();
    $assignment = makeAssignment($section);

    $this->actingAs($instructor)
        ->put(route('assignments.update', $assignment), [
            'title' => 'Updated Assignment',
            'max_score' => 50,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('assignments', [
        'id' => $assignment->id,
        'title' => 'Updated Assignment',
    ]);
});

test('instructor can delete their assignment', function (): void {
    [$instructor, $section] = makeAssignmentSection();
    $assignment = makeAssignment($section);

    $this->actingAs($instructor)
        ->delete(route('assignments.destroy', $assignment))
        ->assertRedirect();

    $this->assertDatabaseMissing('assignments', ['id' => $assignment->id]);
});

// ─── Visibility ───────────────────────────────────────────────────────────────

test('student sees only published assignments in index', function (): void {
    [$instructor, $section] = makeAssignmentSection();
    $published = makeAssignment($section, true);
    $draft = makeAssignment($section, false);

    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentForAssignment($student, $section);

    $response = $this->actingAs($student)
        ->get(route('sections.assignments.index', $section))
        ->assertOk();

    $assignmentsPaginated = $response->original->getData()['page']['props']['assignments'];
    $ids = collect($assignmentsPaginated['data'])->pluck('id')->all();
    expect($ids)->toContain($published->id)
        ->and($ids)->not->toContain($draft->id);
});

test('instructor sees all assignments including drafts', function (): void {
    [$instructor, $section] = makeAssignmentSection();
    $published = makeAssignment($section, true);
    $draft = makeAssignment($section, false);

    $response = $this->actingAs($instructor)
        ->get(route('sections.assignments.index', $section))
        ->assertOk();

    $assignmentsPaginated = $response->original->getData()['page']['props']['assignments'];
    $ids = collect($assignmentsPaginated['data'])->pluck('id')->all();
    expect($ids)->toContain($published->id)
        ->and($ids)->toContain($draft->id);
});

// ─── Publish / Unpublish ──────────────────────────────────────────────────────

test('instructor can publish an assignment', function (): void {
    [$instructor, $section] = makeAssignmentSection();
    $assignment = makeAssignment($section);

    $this->actingAs($instructor)
        ->post(route('assignments.publish', $assignment))
        ->assertRedirect();

    expect($assignment->fresh()->published_at)->not->toBeNull();
});

test('instructor can unpublish an assignment', function (): void {
    [$instructor, $section] = makeAssignmentSection();
    $assignment = makeAssignment($section, true);

    $this->actingAs($instructor)
        ->post(route('assignments.publish', $assignment))
        ->assertRedirect();

    expect($assignment->fresh()->published_at)->toBeNull();
});

test('student cannot publish an assignment', function (): void {
    [, $section] = makeAssignmentSection();
    $assignment = makeAssignment($section);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->post(route('assignments.publish', $assignment))
        ->assertForbidden();
});

// ─── Ownership (non-owning instructor) ───────────────────────────────────────

test('non-owning instructor cannot create assignment in another section', function (): void {
    [, $section] = makeAssignmentSection();
    $other = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($other)
        ->post(route('sections.assignments.store', $section), [
            'title' => 'Hijack',
            'max_score' => 100,
        ])
        ->assertForbidden();
});

test('non-owning instructor cannot update another instructor\'s assignment', function (): void {
    [, $section] = makeAssignmentSection();
    $assignment = makeAssignment($section);
    $other = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($other)
        ->put(route('assignments.update', $assignment), [
            'title' => 'Hijack',
            'max_score' => 50,
        ])
        ->assertForbidden();
});

test('non-owning instructor cannot delete another instructor\'s assignment', function (): void {
    [, $section] = makeAssignmentSection();
    $assignment = makeAssignment($section);
    $other = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($other)
        ->delete(route('assignments.destroy', $assignment))
        ->assertForbidden();
});

test('non-owning instructor cannot publish another instructor\'s assignment', function (): void {
    [, $section] = makeAssignmentSection();
    $assignment = makeAssignment($section, false);
    $other = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($other)
        ->post(route('assignments.publish', $assignment))
        ->assertForbidden();
});

// ─── Role gates ───────────────────────────────────────────────────────────────

test('student cannot update an assignment', function (): void {
    [, $section] = makeAssignmentSection();
    $assignment = makeAssignment($section);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->put(route('assignments.update', $assignment), [
            'title' => 'Hack',
            'max_score' => 50,
        ])
        ->assertForbidden();
});

test('student cannot delete an assignment', function (): void {
    [, $section] = makeAssignmentSection();
    $assignment = makeAssignment($section);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->delete(route('assignments.destroy', $assignment))
        ->assertForbidden();
});

// ─── Validation ───────────────────────────────────────────────────────────────

test('assignment creation fails with missing title', function (): void {
    [$instructor, $section] = makeAssignmentSection();

    $this->actingAs($instructor)
        ->post(route('sections.assignments.store', $section), ['max_score' => 100])
        ->assertSessionHasErrors(['title']);
});

test('assignment creation fails with past due_at', function (): void {
    [$instructor, $section] = makeAssignmentSection();

    $this->actingAs($instructor)
        ->post(route('sections.assignments.store', $section), [
            'title' => 'Past Assignment',
            'due_at' => now()->subDay()->format('Y-m-d H:i:s'),
        ])
        ->assertSessionHasErrors(['due_at']);
});
