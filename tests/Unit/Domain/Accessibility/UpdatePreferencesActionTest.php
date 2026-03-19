<?php

declare(strict_types=1);

use App\Domain\Accessibility\Actions\UpdatePreferences;
use App\Domain\Accessibility\Models\UserPreference;
use App\Enums\UserRole;
use App\Models\User;

test('creates user preference on first call', function (): void {
    $user = User::factory()->create(['role' => UserRole::Student]);

    $action = new UpdatePreferences();
    $pref = $action->handle($user, [
        'font_size' => 'large',
        'high_contrast' => true,
        'reduced_motion' => false,
        'simplified_layout' => false,
    ]);

    expect($pref)->toBeInstanceOf(UserPreference::class);
    expect($pref->user_id)->toBe($user->id);
    expect($pref->font_size)->toBe('large');
    expect($pref->high_contrast)->toBeTrue();
});

test('updates existing preference on second call', function (): void {
    $user = User::factory()->create(['role' => UserRole::Student]);

    $action = new UpdatePreferences();
    $action->handle($user, ['font_size' => 'small', 'high_contrast' => false, 'reduced_motion' => false, 'simplified_layout' => false]);
    $pref = $action->handle($user, ['font_size' => 'large', 'high_contrast' => true, 'reduced_motion' => true, 'simplified_layout' => false]);

    expect(UserPreference::where('user_id', $user->id)->count())->toBe(1);
    expect($pref->font_size)->toBe('large');
    expect($pref->high_contrast)->toBeTrue();
    expect($pref->reduced_motion)->toBeTrue();
});
