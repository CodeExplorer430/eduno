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
