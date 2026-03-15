<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;

test('welcome page is accessible to guests', function (): void {
    $this->get('/')->assertOk();
});

test('unauthenticated user is redirected to login from dashboard', function (): void {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('authenticated student can reach the dashboard', function (): void {
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)->get(route('dashboard'))->assertOk();
});

test('authenticated instructor can reach the dashboard', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($instructor)->get(route('dashboard'))->assertOk();
});

test('authenticated admin can reach the admin reports page', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)->get(route('admin.reports.index'))->assertOk();
});
