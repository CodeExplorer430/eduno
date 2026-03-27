<?php

declare(strict_types=1);

use App\Domain\Audit\Models\AuditLog;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;

uses(RefreshDatabase::class);

it('admin can export audit logs as csv', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('admin.audit-logs.export', ['format' => 'csv']));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

it('admin can export audit logs as xlsx', function (): void {
    Excel::fake();

    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)->get(route('admin.audit-logs.export', ['format' => 'xlsx']));

    Excel::assertDownloaded('audit-logs.xlsx');
});

it('admin can export audit logs as pdf', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('admin.audit-logs.export', ['format' => 'pdf']));

    $response->assertStatus(200);
    $response->assertDownload('audit-logs.pdf');
});

it('export respects action filter', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    AuditLog::create([
        'actor_id'    => $admin->id,
        'action'      => 'user.login',
        'entity_type' => User::class,
        'entity_id'   => $admin->id,
        'metadata'    => [],
        'created_at'  => now(),
    ]);

    AuditLog::create([
        'actor_id'    => $admin->id,
        'action'      => 'course.published',
        'entity_type' => User::class,
        'entity_id'   => $admin->id,
        'metadata'    => [],
        'created_at'  => now(),
    ]);

    $response = $this->actingAs($admin)->get(
        route('admin.audit-logs.export', ['format' => 'csv', 'action' => 'user.login'])
    );

    $response->assertStatus(200);
    $content = $response->streamedContent();
    expect($content)->toContain('user.login');
    expect($content)->not->toContain('course.published');
});

it('export respects date range filter', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    AuditLog::create([
        'actor_id'    => $admin->id,
        'action'      => 'old.event',
        'entity_type' => User::class,
        'entity_id'   => $admin->id,
        'metadata'    => [],
        'created_at'  => now()->subDays(10),
    ]);

    AuditLog::create([
        'actor_id'    => $admin->id,
        'action'      => 'recent.event',
        'entity_type' => User::class,
        'entity_id'   => $admin->id,
        'metadata'    => [],
        'created_at'  => now(),
    ]);

    $from = now()->subDays(2)->toDateString();
    $to   = now()->toDateString();

    $response = $this->actingAs($admin)->get(
        route('admin.audit-logs.export', ['format' => 'csv', 'from' => $from, 'to' => $to])
    );

    $response->assertStatus(200);
    $content = $response->streamedContent();
    expect($content)->toContain('recent.event');
    expect($content)->not->toContain('old.event');
});

it('instructor cannot export audit logs', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $response = $this->actingAs($instructor)->get(route('admin.audit-logs.export', ['format' => 'csv']));

    $response->assertForbidden();
});

it('student cannot export audit logs', function (): void {
    $student = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($student)->get(route('admin.audit-logs.export', ['format' => 'csv']));

    $response->assertForbidden();
});
