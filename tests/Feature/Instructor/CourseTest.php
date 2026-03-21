<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use App\Enums\UserRole;
use App\Models\User;

it('instructor can view their course list', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    Course::create([
        'code' => 'CRS101',
        'title' => 'My Course',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    $response = $this->actingAs($instructor)->get(route('instructor.courses.index'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
        ->component('Instructor/Courses/Index')
        ->has('courses')
    );
});

it('student cannot access instructor course list', function () {
    $student = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($student)->get(route('instructor.courses.index'));

    $response->assertStatus(403);
});

it('instructor can create a course', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $response = $this->actingAs($instructor)->post(
        route('instructor.courses.store'),
        [
            'code' => 'CRS102',
            'title' => 'New Course',
            'department' => 'IT',
            'term' => '2nd',
            'academic_year' => '2025-2026',
        ]
    );

    $response->assertRedirect(route('instructor.courses.index'));
    $this->assertDatabaseHas('courses', [
        'code' => 'CRS102',
        'created_by' => $instructor->id,
    ]);
});

it('student cannot create a course', function () {
    $student = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($student)->post(
        route('instructor.courses.store'),
        [
            'code' => 'CRS999',
            'title' => 'Injected Course',
            'department' => 'CS',
            'term' => '1st',
            'academic_year' => '2025-2026',
        ]
    );

    $response->assertStatus(403);
});

it('instructor can update their own course', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'CRS103',
        'title' => 'Original Title',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'draft',
        'created_by' => $instructor->id,
    ]);

    $response = $this->actingAs($instructor)->patch(
        route('instructor.courses.update', $course),
        [
            'code' => 'CRS103',
            'title' => 'Updated Title',
            'department' => 'CS',
            'term' => '1st',
            'academic_year' => '2025-2026',
        ]
    );

    $response->assertRedirect(route('instructor.courses.index'));
    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'title' => 'Updated Title',
    ]);
});

it('instructor cannot update another instructor course', function () {
    $ownerInstructor = User::factory()->create(['role' => UserRole::Instructor]);
    $otherInstructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'CRS104',
        'title' => 'Protected Course',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'draft',
        'created_by' => $ownerInstructor->id,
    ]);

    $response = $this->actingAs($otherInstructor)->patch(
        route('instructor.courses.update', $course),
        [
            'code' => 'CRS104',
            'title' => 'Hijacked Title',
            'department' => 'CS',
            'term' => '1st',
            'academic_year' => '2025-2026',
        ]
    );

    $response->assertStatus(403);
});
