<?php

declare(strict_types=1);

use App\Domain\Notification\Actions\GetUserNotifications;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function insertNotification(int $userId, string $message = 'Test'): string
{
    $id = (string) Str::uuid();

    DB::table('notifications')->insert([
        'id'              => $id,
        'type'            => 'App\\Notifications\\GradeReleasedNotification',
        'notifiable_type' => 'App\\Models\\User',
        'notifiable_id'   => $userId,
        'data'            => json_encode(['message' => $message, 'url' => '/', 'type' => 'grade_released']),
        'read_at'         => null,
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);

    return $id;
}

test('returns paginator with 15 per page', function (): void {
    $user = User::factory()->create();
    insertNotification($user->id);

    $this->actingAs($user);

    $result = (new GetUserNotifications())->execute();

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
    expect($result->perPage())->toBe(15);
});

test('only returns notifications for the authenticated user', function (): void {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    insertNotification($userA->id, 'Notif for A');
    insertNotification($userA->id, 'Notif for A 2');
    insertNotification($userB->id, 'Notif for B');

    $this->actingAs($userA);
    $resultA = (new GetUserNotifications())->execute();

    expect($resultA->total())->toBe(2);

    $this->actingAs($userB);
    $resultB = (new GetUserNotifications())->execute();

    expect($resultB->total())->toBe(1);
});
