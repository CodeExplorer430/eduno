<?php

declare(strict_types=1);

use App\Domain\Accessibility\Models\UserPreference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can show accessibility preferences form', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('profile.accessibility.edit'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('Profile/Accessibility'));
});

it('saves valid preferences', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('profile.accessibility.update'), [
        'font_size' => 'large',
        'high_contrast' => true,
        'reduced_motion' => false,
        'simplified_layout' => true,
        'language' => 'en',
    ]);

    $response->assertRedirect(route('profile.accessibility.edit'));
    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('user_preferences', [
        'user_id' => $user->id,
        'font_size' => 'large',
        'high_contrast' => true,
        'reduced_motion' => false,
        'simplified_layout' => true,
        'language' => 'en',
    ]);
});

it('rejects invalid font_size', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('profile.accessibility.update'), [
        'font_size' => 'gigantic',
        'high_contrast' => false,
        'reduced_motion' => false,
        'simplified_layout' => false,
        'language' => 'en',
    ]);

    $response->assertSessionHasErrors('font_size');
});

it('creates preferences record when none exists', function () {
    $user = User::factory()->create();

    expect(UserPreference::where('user_id', $user->id)->exists())->toBeFalse();

    $this->actingAs($user)->patch(route('profile.accessibility.update'), [
        'font_size' => 'medium',
        'high_contrast' => false,
        'reduced_motion' => false,
        'simplified_layout' => false,
        'language' => 'en',
    ]);

    expect(UserPreference::where('user_id', $user->id)->exists())->toBeTrue();
});

it('updates existing preferences on subsequent save', function () {
    $user = User::factory()->create();
    UserPreference::create([
        'user_id' => $user->id,
        'font_size' => 'small',
        'high_contrast' => false,
        'reduced_motion' => false,
        'simplified_layout' => false,
        'language' => 'en',
    ]);

    $this->actingAs($user)->patch(route('profile.accessibility.update'), [
        'font_size' => 'xlarge',
        'high_contrast' => true,
        'reduced_motion' => true,
        'simplified_layout' => false,
        'language' => 'en',
    ]);

    $this->assertDatabaseHas('user_preferences', [
        'user_id' => $user->id,
        'font_size' => 'xlarge',
        'high_contrast' => true,
        'reduced_motion' => true,
    ]);

    expect(UserPreference::where('user_id', $user->id)->count())->toBe(1);
});
