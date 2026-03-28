<?php

declare(strict_types=1);

use App\Domain\Notification\Actions\MarkNotificationRead;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

test('marks the notification as read', function (): void {
    $user = User::factory()->create();
    $notifId = (string) Str::uuid();

    DB::table('notifications')->insert([
        'id'              => $notifId,
        'type'            => 'App\\Notifications\\GradeReleasedNotification',
        'notifiable_type' => 'App\\Models\\User',
        'notifiable_id'   => $user->id,
        'data'            => json_encode(['message' => 'Test', 'url' => '/', 'type' => 'grade_released']),
        'read_at'         => null,
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);

    $this->actingAs($user);

    (new MarkNotificationRead())->execute($notifId);

    expect(
        DB::table('notifications')
            ->where('id', $notifId)
            ->whereNotNull('read_at')
            ->exists()
    )->toBeTrue();
});

test('throws 404 when notification belongs to another user', function (): void {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $notifId = (string) Str::uuid();

    DB::table('notifications')->insert([
        'id'              => $notifId,
        'type'            => 'App\\Notifications\\GradeReleasedNotification',
        'notifiable_type' => 'App\\Models\\User',
        'notifiable_id'   => $owner->id,
        'data'            => json_encode(['message' => 'Secret', 'url' => '/', 'type' => 'grade_released']),
        'read_at'         => null,
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);

    $this->actingAs($other);

    expect(fn () => (new MarkNotificationRead())->execute($notifId))
        ->toThrow(ModelNotFoundException::class);
});
