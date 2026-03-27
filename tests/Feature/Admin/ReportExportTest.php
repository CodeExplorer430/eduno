<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;

uses(RefreshDatabase::class);

it('admin can export submissions as csv', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('admin.reports.export', ['format' => 'csv']));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

it('admin can export submissions as xlsx', function (): void {
    Excel::fake();

    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)->get(route('admin.reports.export', ['format' => 'xlsx']));

    Excel::assertDownloaded('submissions.xlsx');
});

it('admin can export submissions as pdf', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('admin.reports.export', ['format' => 'pdf']));

    $response->assertStatus(200);
    $response->assertDownload('submissions.pdf');
});

it('instructor cannot export submissions', function (): void {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $response = $this->actingAs($instructor)->get(route('admin.reports.export', ['format' => 'csv']));

    $response->assertForbidden();
});

it('student cannot export submissions', function (): void {
    $student = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($student)->get(route('admin.reports.export', ['format' => 'csv']));

    $response->assertForbidden();
});
