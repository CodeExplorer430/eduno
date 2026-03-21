<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use App\Enums\UserRole;
use App\Models\User;

function createCourse(User $instructor, string $code = 'CS101'): Course
{
    return Course::create([
        'code' => $code,
        'title' => 'Test Course',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
        'status' => 'draft',
        'created_by' => $instructor->id,
    ]);
}

test('unauthenticated user is redirected from courses index', function (): void {
    $this->get(route('courses.index'))
        ->assertRedirect(route('login'));
});

test('authenticated user can view courses index', function (): void {
    $user = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($user)
        ->get(route('courses.index'))
        ->assertOk();
});

test('instructor can view course create form', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($instructor)
        ->get(route('courses.create'))
        ->assertOk();
});

test('student is forbidden from course create form', function (): void {
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->get(route('courses.create'))
        ->assertForbidden();
});

test('instructor can create a course', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($instructor)
        ->post(route('courses.store'), [
            'code' => 'CCS300',
            'title' => 'Advanced Topics',
            'department' => 'CCS',
            'term' => '1st Semester',
            'academic_year' => '2025-2026',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('courses', [
        'code' => 'CCS300',
        'created_by' => $instructor->id,
    ]);
});

test('student cannot create a course', function (): void {
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->post(route('courses.store'), [
            'code' => 'CCS300',
            'title' => 'Advanced Topics',
            'department' => 'CCS',
            'term' => '1st Semester',
            'academic_year' => '2025-2026',
        ])
        ->assertForbidden();
});

test('instructor can view their own course', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = createCourse($instructor);

    $this->actingAs($instructor)
        ->get(route('courses.show', $course))
        ->assertOk();
});

test('instructor can edit their own course', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = createCourse($instructor);

    $this->actingAs($instructor)
        ->get(route('courses.edit', $course))
        ->assertOk();
});

test('instructor cannot edit another instructor course', function (): void {
    $owner = User::factory()->create(['role' => UserRole::Instructor]);
    $other = User::factory()->create(['role' => UserRole::Instructor]);
    $course = createCourse($owner);

    $this->actingAs($other)
        ->get(route('courses.edit', $course))
        ->assertForbidden();
});

test('instructor can update their own course', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = createCourse($instructor);

    $this->actingAs($instructor)
        ->put(route('courses.update', $course), [
            'code' => 'CS101',
            'title' => 'Updated Title',
            'department' => 'CCS',
            'term' => '1st Semester',
            'academic_year' => '2025-2026',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'title' => 'Updated Title',
    ]);
});

test('instructor can delete their own course', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = createCourse($instructor);

    $this->actingAs($instructor)
        ->delete(route('courses.destroy', $course))
        ->assertRedirect(route('courses.index'));

    $this->assertDatabaseMissing('courses', ['id' => $course->id]);
});

test('admin can update any course', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $course = createCourse($instructor);

    $this->actingAs($admin)
        ->put(route('courses.update', $course), [
            'code' => 'CS101',
            'title' => 'Admin Updated',
            'department' => 'CCS',
            'term' => '1st Semester',
            'academic_year' => '2025-2026',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'title' => 'Admin Updated',
    ]);
});

test('course creation fails with missing required fields', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($instructor)
        ->post(route('courses.store'), [])
        ->assertSessionHasErrors(['code', 'title', 'department', 'term', 'academic_year']);
});

test('course creation fails with duplicate code', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    createCourse($instructor, 'CS101');

    $this->actingAs($instructor)
        ->post(route('courses.store'), [
            'code' => 'CS101',
            'title' => 'Another Course',
            'department' => 'CCS',
            'term' => '1st Semester',
            'academic_year' => '2025-2026',
        ])
        ->assertSessionHasErrors(['code']);
});

// ─── Ownership / role gates ───────────────────────────────────────────────────

test('non-owning instructor cannot delete another instructor\'s course', function (): void {
    $owner = User::factory()->create(['role' => UserRole::Instructor]);
    $other = User::factory()->create(['role' => UserRole::Instructor]);
    $course = createCourse($owner, 'TST'.fake()->numberBetween(100, 999));

    $this->actingAs($other)
        ->delete(route('courses.destroy', $course))
        ->assertForbidden();
});

test('student cannot update a course', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);
    $course = createCourse($instructor, 'TST'.fake()->numberBetween(100, 999));

    $this->actingAs($student)
        ->put(route('courses.update', $course), [
            'code' => 'HACK101',
            'title' => 'Hacked',
            'department' => 'CCS',
            'term' => '1st Semester',
            'academic_year' => '2025-2026',
        ])
        ->assertForbidden();
});

test('student cannot delete a course', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);
    $course = createCourse($instructor, 'TST'.fake()->numberBetween(100, 999));

    $this->actingAs($student)
        ->delete(route('courses.destroy', $course))
        ->assertForbidden();
});
