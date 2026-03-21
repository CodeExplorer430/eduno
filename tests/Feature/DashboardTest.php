<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;

it('student dashboard returns correct role and props', function () {
    $student = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($student)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
            ->component('Dashboard')
            ->where('role', 'student')
            ->has('enrolled_courses_count')
            ->has('upcoming_assignments')
            ->has('recent_announcements')
    );
});

it('instructor dashboard returns correct role and props', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $response = $this->actingAs($instructor)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
            ->component('Dashboard')
            ->where('role', 'instructor')
            ->has('courses_count')
            ->has('pending_submissions_count')
            ->has('recent_submissions')
            ->has('upcoming_deadlines')
    );
});

it('admin dashboard returns correct role and props', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
            ->component('Dashboard')
            ->where('role', 'admin')
            ->has('users_by_role')
            ->has('total_courses')
            ->has('total_submissions')
            ->has('total_grades_released')
    );
});

it('guest is redirected from dashboard', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});
