<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;

test('unauthenticated user is redirected from preferences.edit', function (): void {
    $response = $this->get(route('preferences.edit'));

    $response->assertRedirect(route('login'));
});

test('authenticated user can view preferences.edit', function (): void {
    $user = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($user)->get(route('preferences.edit'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('Profile/Accessibility'));
});

test('valid put to preferences.update updates the db record and redirects', function (): void {
    $user = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($user)->put(route('preferences.update'), [
        'font_size' => 'large',
        'high_contrast' => true,
        'reduced_motion' => false,
        'simplified_layout' => false,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('user_preferences', [
        'user_id' => $user->id,
        'font_size' => 'large',
        'high_contrast' => true,
    ]);
});

test('invalid font_size value returns validation error', function (): void {
    $user = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($user)->put(route('preferences.update'), [
        'font_size' => 'gigantic',
        'high_contrast' => false,
        'reduced_motion' => false,
        'simplified_layout' => false,
    ]);

    $response->assertSessionHasErrors(['font_size']);
});
