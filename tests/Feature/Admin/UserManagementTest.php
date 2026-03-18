<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;

it('admin can access user management', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('admin.users.index'));

    $response->assertStatus(200);
});

it('non-admin cannot access admin area', function () {
    $student = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($student)->get(route('admin.users.index'));

    $response->assertStatus(403);
});

it('admin can view user edit form', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $target = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($admin)->get(route('admin.users.edit', $target));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('Admin/Users/Edit'));
});

it('admin can update a user role', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $target = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($admin)->patch(
        route('admin.users.update', $target),
        ['role' => 'instructor']
    );

    $response->assertRedirect(route('admin.users.index'));
    $this->assertDatabaseHas('users', ['id' => $target->id, 'role' => 'instructor']);
});

it('non-admin cannot update user role', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $target = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($instructor)->patch(
        route('admin.users.update', $target),
        ['role' => 'instructor']
    );

    $response->assertStatus(403);
});

it('invalid role value is rejected', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $target = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($admin)->patch(
        route('admin.users.update', $target),
        ['role' => 'superuser']
    );

    $response->assertSessionHasErrors('role');
});

it('guest is redirected from user edit page', function () {
    $target = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->get(route('admin.users.edit', $target));

    $response->assertRedirect(route('login'));
});
