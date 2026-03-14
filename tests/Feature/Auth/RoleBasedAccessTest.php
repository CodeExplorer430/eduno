<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;
use App\Policies\UserPolicy;

test('admin can view any user', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin);

    expect((new UserPolicy)->viewAny($admin))->toBeTrue();
});

test('student cannot view any user', function (): void {
    $student = User::factory()->create(['role' => UserRole::Student]);

    expect((new UserPolicy)->viewAny($student))->toBeFalse();
});

test('instructor cannot view any user', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    expect((new UserPolicy)->viewAny($instructor))->toBeFalse();
});

test('user can view their own profile', function (): void {
    $user = User::factory()->create(['role' => UserRole::Student]);

    expect((new UserPolicy)->view($user, $user))->toBeTrue();
});

test('student cannot view another student profile', function (): void {
    $student = User::factory()->create(['role' => UserRole::Student]);
    $other = User::factory()->create(['role' => UserRole::Student]);

    expect((new UserPolicy)->view($student, $other))->toBeFalse();
});

test('admin can view any individual user profile', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    expect((new UserPolicy)->view($admin, $student))->toBeTrue();
});

test('admin cannot delete themselves', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    expect((new UserPolicy)->delete($admin, $admin))->toBeFalse();
});

test('admin can delete another user', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    expect((new UserPolicy)->delete($admin, $student))->toBeTrue();
});

test('student cannot delete any user', function (): void {
    $student = User::factory()->create(['role' => UserRole::Student]);
    $other = User::factory()->create(['role' => UserRole::Student]);

    expect((new UserPolicy)->delete($student, $other))->toBeFalse();
});

test('authenticated user can access dashboard', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});

test('unauthenticated user is redirected from dashboard', function (): void {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});
