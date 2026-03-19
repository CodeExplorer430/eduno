<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;

test('admin can view admin reports index', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('admin.reports.index'));

    $response->assertOk();
    $response->assertInertia(
        fn ($page) => $page
        ->component('Admin/Reports/Index')
        ->has('report.total_courses')
        ->has('report.total_sections')
        ->has('report.total_students')
        ->has('report.total_submissions')
        ->has('report.late_submissions')
        ->has('report.graded_submissions')
    );
});

test('instructor gets 403 on admin reports', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $response = $this->actingAs($instructor)->get(route('admin.reports.index'));

    $response->assertForbidden();
});

test('student gets 403 on admin reports', function (): void {
    $student = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($student)->get(route('admin.reports.index'));

    $response->assertForbidden();
});
