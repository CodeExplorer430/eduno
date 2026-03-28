<?php

declare(strict_types=1);

use App\Domain\Notification\Actions\MarkAllNotificationsRead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

test('marks all unread notifications as read', function (): void {
    $user = User::factory()->create();

    foreach (range(1, 3) as $i) {
        DB::table('notifications')->insert([
            'id'              => (string) Str::uuid(),
            'type'            => 'App\\Notifications\\GradeReleasedNotification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id'   => $user->id,
            'data'            => json_encode(['message' => "Notification {$i}", 'url' => '/', 'type' => 'grade_released']),
            'read_at'         => null,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }

    $this->actingAs($user);

    (new MarkAllNotificationsRead())->execute();

    expect(
        DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->count()
    )->toBe(0);
});

test('already-read notifications are unaffected by marking all as read', function (): void {
    $user = User::factory()->create();
    $readAt = now()->subHour();

    // One already-read notification
    $readId = (string) Str::uuid();
    DB::table('notifications')->insert([
        'id'              => $readId,
        'type'            => 'App\\Notifications\\GradeReleasedNotification',
        'notifiable_type' => 'App\\Models\\User',
        'notifiable_id'   => $user->id,
        'data'            => json_encode(['message' => 'Already read', 'url' => '/', 'type' => 'grade_released']),
        'read_at'         => $readAt,
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);

    // One unread notification
    DB::table('notifications')->insert([
        'id'              => (string) Str::uuid(),
        'type'            => 'App\\Notifications\\GradeReleasedNotification',
        'notifiable_type' => 'App\\Models\\User',
        'notifiable_id'   => $user->id,
        'data'            => json_encode(['message' => 'Unread', 'url' => '/', 'type' => 'grade_released']),
        'read_at'         => null,
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);

    $this->actingAs($user);

    (new MarkAllNotificationsRead())->execute();

    // All 2 notifications now have read_at set
    expect(
        DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->whereNotNull('read_at')
            ->count()
    )->toBe(2);

    // The already-read notification's read_at timestamp was not changed
    $originalReadAt = DB::table('notifications')
        ->where('id', $readId)
        ->value('read_at');

    expect($originalReadAt)->toBe($readAt->toDateTimeString());
});
