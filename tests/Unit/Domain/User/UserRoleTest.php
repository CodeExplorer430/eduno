<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;

test('user defaults to student role', function (): void {
    $user = User::factory()->make();

    expect($user->role)->toBe(UserRole::Student);
});

test('hasRole returns true for matching role', function (): void {
    $student = User::factory()->make(['role' => UserRole::Student]);
    $instructor = User::factory()->make(['role' => UserRole::Instructor]);
    $admin = User::factory()->make(['role' => UserRole::Admin]);

    expect($student->hasRole(UserRole::Student))->toBeTrue()
        ->and($instructor->hasRole(UserRole::Instructor))->toBeTrue()
        ->and($admin->hasRole(UserRole::Admin))->toBeTrue();
});

test('hasRole returns false for non-matching role', function (): void {
    $student = User::factory()->make(['role' => UserRole::Student]);

    expect($student->hasRole(UserRole::Instructor))->toBeFalse()
        ->and($student->hasRole(UserRole::Admin))->toBeFalse();
});

test('isStudent helper returns correct boolean', function (): void {
    $student = User::factory()->make(['role' => UserRole::Student]);
    $instructor = User::factory()->make(['role' => UserRole::Instructor]);

    expect($student->isStudent())->toBeTrue()
        ->and($instructor->isStudent())->toBeFalse();
});

test('isInstructor helper returns correct boolean', function (): void {
    $instructor = User::factory()->make(['role' => UserRole::Instructor]);
    $student = User::factory()->make(['role' => UserRole::Student]);

    expect($instructor->isInstructor())->toBeTrue()
        ->and($student->isInstructor())->toBeFalse();
});

test('isAdmin helper returns correct boolean', function (): void {
    $admin = User::factory()->make(['role' => UserRole::Admin]);
    $student = User::factory()->make(['role' => UserRole::Student]);

    expect($admin->isAdmin())->toBeTrue()
        ->and($student->isAdmin())->toBeFalse();
});

test('role is cast to UserRole enum', function (): void {
    $user = User::factory()->make(['role' => 'instructor']);

    expect($user->role)->toBeInstanceOf(UserRole::class)
        ->and($user->role)->toBe(UserRole::Instructor);
});
