<?php

declare(strict_types=1);

use App\Domain\Accessibility\Models\UserPreference;
use App\Domain\Submission\Models\Submission;
use App\Models\User;
use App\Notifications\NewSubmissionNotification;

it('includes mail channel when instructor has no preferences', function () {
    $instructor = User::factory()->make();
    $instructor->setRelation('preferences', null);

    $notification = new NewSubmissionNotification(new Submission());

    $channels = $notification->via($instructor);

    expect($channels)->toContain('database');
    expect($channels)->toContain('mail');
});

it('includes mail channel when email_notifications is true', function () {
    $instructor = User::factory()->make();
    $instructor->setRelation('preferences', new UserPreference(['email_notifications' => true]));

    $notification = new NewSubmissionNotification(new Submission());

    $channels = $notification->via($instructor);

    expect($channels)->toContain('mail');
    expect($channels)->toContain('database');
});

it('excludes mail channel when email_notifications is false', function () {
    $instructor = User::factory()->make();
    $instructor->setRelation('preferences', new UserPreference(['email_notifications' => false]));

    $notification = new NewSubmissionNotification(new Submission());

    $channels = $notification->via($instructor);

    expect($channels)->not->toContain('mail');
    expect($channels)->toContain('database');
});

it('always includes database channel regardless of email preferences', function () {
    $instructor = User::factory()->make();
    $instructor->setRelation('preferences', new UserPreference(['email_notifications' => false]));

    $notification = new NewSubmissionNotification(new Submission());

    $channels = $notification->via($instructor);

    expect($channels)->toContain('database');
});
