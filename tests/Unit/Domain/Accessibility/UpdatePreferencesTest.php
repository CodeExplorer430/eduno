<?php

declare(strict_types=1);

use App\Domain\Accessibility\Actions\UpdatePreferences;
use App\Domain\Accessibility\Models\UserPreference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates preferences when none exist', function () {
    $user = User::factory()->create();
    $action = new UpdatePreferences();

    $pref = $action->handle($user, [
        'dyslexia_font' => true,
        'reduced_motion' => false,
        'high_contrast' => false,
    ]);

    expect($pref)->toBeInstanceOf(UserPreference::class)
        ->and($pref->dyslexia_font)->toBeTrue()
        ->and($pref->user_id)->toBe($user->id);
});

it('updates preferences when they already exist', function () {
    $user = User::factory()->create();
    UserPreference::create([
        'user_id' => $user->id,
        'dyslexia_font' => false,
        'reduced_motion' => false,
        'high_contrast' => false,
    ]);

    $action = new UpdatePreferences();
    $action->handle($user, ['dyslexia_font' => true]);

    expect(UserPreference::where('user_id', $user->id)->value('dyslexia_font'))->toBeTrue();
});

it('only creates one preferences record per user', function () {
    $user = User::factory()->create();
    $action = new UpdatePreferences();

    $action->handle($user, ['high_contrast' => true]);
    $action->handle($user, ['high_contrast' => false]);

    expect(UserPreference::where('user_id', $user->id)->count())->toBe(1);
});
