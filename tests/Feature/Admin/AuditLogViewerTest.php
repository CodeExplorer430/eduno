<?php

declare(strict_types=1);

use App\Domain\Audit\Models\AuditLog;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('admin can view audit logs', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('admin.audit-logs.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Admin/AuditLogs/Index')
        ->has('logs')
    );
});

it('non-admin cannot access audit logs', function () {
    $student = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($student)->get(route('admin.audit-logs.index'));

    $response->assertStatus(403);
});

it('returns logs ordered newest first', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $older = AuditLog::create([
        'actor_id' => $admin->id,
        'action' => 'user.login',
        'entity_type' => User::class,
        'entity_id' => $admin->id,
        'metadata' => [],
        'created_at' => now()->subHour(),
    ]);

    $newer = AuditLog::create([
        'actor_id' => $admin->id,
        'action' => 'user.logout',
        'entity_type' => User::class,
        'entity_id' => $admin->id,
        'metadata' => [],
        'created_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.audit-logs.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->where('logs.data.0.id', $newer->id)
    );
});

it('can filter audit logs by action', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    AuditLog::create([
        'actor_id' => $admin->id,
        'action' => 'user.role_changed',
        'entity_type' => User::class,
        'entity_id' => $admin->id,
        'metadata' => ['from' => 'student', 'to' => 'admin'],
        'created_at' => now(),
    ]);

    AuditLog::create([
        'actor_id' => $admin->id,
        'action' => 'course.status_changed',
        'entity_type' => User::class,
        'entity_id' => $admin->id,
        'metadata' => [],
        'created_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(
        route('admin.audit-logs.index', ['action' => 'user.role_changed'])
    );

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->has('logs.data', 1)
        ->where('logs.data.0.action', 'user.role_changed')
    );
});

it('can filter audit logs by actor email', function () {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
        'email' => 'admin@example.com',
    ]);
    $other = User::factory()->create([
        'role' => UserRole::Admin,
        'email' => 'other@example.com',
    ]);

    AuditLog::create([
        'actor_id' => $admin->id,
        'action' => 'user.login',
        'entity_type' => User::class,
        'entity_id' => $admin->id,
        'metadata' => [],
        'created_at' => now(),
    ]);

    AuditLog::create([
        'actor_id' => $other->id,
        'action' => 'user.login',
        'entity_type' => User::class,
        'entity_id' => $other->id,
        'metadata' => [],
        'created_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(
        route('admin.audit-logs.index', ['actor_email' => 'admin@example.com'])
    );

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->has('logs.data', 1)
        ->where('logs.data.0.actor_id', $admin->id)
    );
});
