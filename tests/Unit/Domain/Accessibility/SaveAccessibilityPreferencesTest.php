<?php

declare(strict_types=1);

use App\Domain\Accessibility\Actions\SaveAccessibilityPreferences;
use App\Domain\Accessibility\Models\UserPreference;
use App\Domain\Audit\Actions\LogAction;
use App\Enums\UserRole;
use App\Models\User;

it('creates a UserPreference record for the user', function () {
    $logAction = Mockery::mock(LogAction::class);
    $logAction->shouldReceive('execute')->once();

    $action = new SaveAccessibilityPreferences($logAction);

    $user = User::factory()->create(['role' => UserRole::Student]);

    $preferences = $action->execute($user, 'medium', false, false, false, 'en');

    expect($preferences)->toBeInstanceOf(UserPreference::class);
    expect($preferences->user_id)->toBe($user->id);
    expect($preferences->font_size)->toBe('medium');
    expect($preferences->high_contrast)->toBeFalse();

    $this->assertDatabaseHas('user_preferences', [
        'user_id' => $user->id,
        'font_size' => 'medium',
        'high_contrast' => false,
    ]);
});

it('updates an existing UserPreference instead of creating a duplicate', function () {
    $logAction = Mockery::mock(LogAction::class);
    $logAction->shouldReceive('execute')->twice();

    $action = new SaveAccessibilityPreferences($logAction);

    $user = User::factory()->create(['role' => UserRole::Student]);

    $action->execute($user, 'medium', false, false, false, 'en');
    $action->execute($user, 'large', true, false, false, 'en');

    expect(UserPreference::where('user_id', $user->id)->count())->toBe(1);
    $this->assertDatabaseHas('user_preferences', [
        'user_id' => $user->id,
        'font_size' => 'large',
        'high_contrast' => true,
    ]);
});
