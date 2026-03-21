<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('admin can view course list', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('admin.courses.index'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
        ->component('Admin/Courses/Index')
        ->has('courses')
    );
});

it('non-admin cannot access course management', function () {
    $student = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($student)->get(route('admin.courses.index'));

    $response->assertStatus(403);
});

it('admin can filter courses by status', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Course::create([
        'code' => 'CS101',
        'title' => 'Draft Course',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'draft',
        'created_by' => $admin->id,
    ]);

    Course::create([
        'code' => 'CS102',
        'title' => 'Published Course',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $admin->id,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.courses.index', ['status' => 'draft']));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
        ->component('Admin/Courses/Index')
        ->has('courses.data', 1)
    );
});

it('admin can update course status', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $course = Course::create([
        'code' => 'CS103',
        'title' => 'Status Test Course',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'draft',
        'created_by' => $admin->id,
    ]);

    $response = $this->actingAs($admin)->patch(
        route('admin.courses.updateStatus', $course),
        ['status' => 'archived']
    );

    $response->assertRedirect(route('admin.courses.index'));
    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'status' => 'archived',
    ]);
});

it('non-admin cannot update course status', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $course = Course::create([
        'code' => 'CS104',
        'title' => 'Instructor Block Course',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'draft',
        'created_by' => $admin->id,
    ]);

    $response = $this->actingAs($instructor)->patch(
        route('admin.courses.updateStatus', $course),
        ['status' => 'published']
    );

    $response->assertStatus(403);
});

it('status update rejects invalid status value', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $course = Course::create([
        'code' => 'CS105',
        'title' => 'Validation Test Course',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'draft',
        'created_by' => $admin->id,
    ]);

    $response = $this->actingAs($admin)->patch(
        route('admin.courses.updateStatus', $course),
        ['status' => 'invalid']
    );

    $response->assertSessionHasErrors('status');
});
