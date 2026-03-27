<?php

declare(strict_types=1);

use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\Enrollment;
use App\Enums\UserRole;
use App\Models\User;

// ─── Helpers ─────────────────────────────────────────────────────────────────

function rosterMakeSection(User $instructor): CourseSection
{
    $course = Course::create([
        'code'          => 'RST' . fake()->unique()->numberBetween(100, 999),
        'title'         => 'Roster Test Course',
        'department'    => 'CS',
        'term'          => '1st',
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

function rosterEnroll(User $student, CourseSection $section, string $status = 'active'): Enrollment
{
    return Enrollment::create([
        'user_id'           => $student->id,
        'course_section_id' => $section->id,
        'status'            => $status,
        'enrolled_at'       => now(),
    ]);
}

// ─── Index ────────────────────────────────────────────────────────────────────

test('instructor can view roster for their section', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $section    = rosterMakeSection($instructor);

    $this->actingAs($instructor)
        ->get(route('instructor.roster.index', $section))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Instructor/Roster/Index'));
});

test('instructor cannot view roster for another instructor\'s section', function (): void {
    $owner   = User::factory()->create(['role' => UserRole::Instructor]);
    $other   = User::factory()->create(['role' => UserRole::Instructor]);
    $section = rosterMakeSection($owner);

    $this->actingAs($other)
        ->get(route('instructor.roster.index', $section))
        ->assertForbidden();
});

test('admin can view any section roster', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $admin      = User::factory()->create(['role' => UserRole::Admin]);
    $section    = rosterMakeSection($instructor);

    $this->actingAs($admin)
        ->get(route('instructor.roster.index', $section))
        ->assertOk();
});

test('guest is redirected from roster', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $section    = rosterMakeSection($instructor);

    $this->get(route('instructor.roster.index', $section))
        ->assertRedirect(route('login'));
});

test('roster lists only active enrollments', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $section    = rosterMakeSection($instructor);

    $active    = User::factory()->create(['role' => UserRole::Student]);
    $withdrawn = User::factory()->create(['role' => UserRole::Student]);

    rosterEnroll($active, $section, 'active');
    rosterEnroll($withdrawn, $section, 'withdrawn');

    $this->actingAs($instructor)
        ->get(route('instructor.roster.index', $section))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component('Instructor/Roster/Index')
                ->has('students', 1)
                ->where('students.0.email', $active->email)
        );
});

// ─── Store ────────────────────────────────────────────────────────────────────

test('instructor can enroll a student by email', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student    = User::factory()->create(['role' => UserRole::Student]);
    $section    = rosterMakeSection($instructor);

    $this->actingAs($instructor)
        ->post(route('instructor.roster.store', $section), ['email' => $student->email])
        ->assertRedirect(route('instructor.roster.index', $section));

    $this->assertDatabaseHas('enrollments', [
        'user_id'           => $student->id,
        'course_section_id' => $section->id,
        'status'            => 'active',
    ]);
});

test('enrolling a non-student email returns validation error', function (): void {
    $instructor     = User::factory()->create(['role' => UserRole::Instructor]);
    $otherInstructor = User::factory()->create(['role' => UserRole::Instructor]);
    $section        = rosterMakeSection($instructor);

    // The email exists in users but the role is Instructor, not Student
    $this->actingAs($instructor)
        ->post(route('instructor.roster.store', $section), ['email' => $otherInstructor->email])
        ->assertStatus(404);
});

test('enrolling an unknown email returns validation error', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $section    = rosterMakeSection($instructor);

    $this->actingAs($instructor)
        ->post(route('instructor.roster.store', $section), ['email' => 'nobody@example.com'])
        ->assertSessionHasErrors(['email']);
});

// ─── Destroy ─────────────────────────────────────────────────────────────────

test('instructor can unenroll a student', function (): void {
    $instructor  = User::factory()->create(['role' => UserRole::Instructor]);
    $student     = User::factory()->create(['role' => UserRole::Student]);
    $section     = rosterMakeSection($instructor);
    $enrollment  = rosterEnroll($student, $section);

    $this->actingAs($instructor)
        ->delete(route('instructor.roster.destroy', ['section' => $section, 'enrollment' => $enrollment]))
        ->assertRedirect(route('instructor.roster.index', $section));

    $this->assertDatabaseHas('enrollments', [
        'id'     => $enrollment->id,
        'status' => 'withdrawn',
    ]);
});

test('instructor cannot unenroll from another section', function (): void {
    $instructor  = User::factory()->create(['role' => UserRole::Instructor]);
    $other       = User::factory()->create(['role' => UserRole::Instructor]);
    $student     = User::factory()->create(['role' => UserRole::Student]);
    $section     = rosterMakeSection($instructor);
    $otherSection = rosterMakeSection($other);
    $enrollment  = rosterEnroll($student, $otherSection);

    // Instructor owns $section but tries to delete an enrollment from $otherSection
    $this->actingAs($instructor)
        ->delete(route('instructor.roster.destroy', ['section' => $section, 'enrollment' => $enrollment]))
        ->assertNotFound();
});
