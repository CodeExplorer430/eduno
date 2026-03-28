<?php

declare(strict_types=1);

use App\Domain\Audit\Models\AuditLog;
use App\Domain\Report\Actions\ExportAuditLogsToCsv;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\StreamedResponse;

uses(RefreshDatabase::class);

function makeCsvAuditLog(array $attributes = []): AuditLog
{
    return AuditLog::create(array_merge([
        'action'      => 'grade_updated',
        'entity_type' => 'Grade',
        'entity_id'   => 1,
        'actor_id'    => null,
    ], $attributes));
}

function captureCsv(StreamedResponse $response): string
{
    ob_start();
    $response->sendContent();

    return (string) ob_get_clean();
}

test('response is a StreamedResponse with text/csv content-type', function (): void {
    $response = (new ExportAuditLogsToCsv())(collect());

    expect($response)->toBeInstanceOf(StreamedResponse::class);
    expect($response->headers->get('Content-Type'))->toContain('text/csv');
});

test('response has content-disposition attachment with audit-logs filename', function (): void {
    $response = (new ExportAuditLogsToCsv())(collect());

    expect($response->headers->get('Content-Disposition'))->toContain('audit-logs.csv');
});

test('CSV output contains the expected header row', function (): void {
    $response = (new ExportAuditLogsToCsv())(collect());
    $csv      = captureCsv($response);
    $firstLine = explode("\n", $csv)[0];

    expect($firstLine)->toContain('ID');
    expect($firstLine)->toContain('Actor');
    expect($firstLine)->toContain('Action');
    expect($firstLine)->toContain('Date');
});

test('CSV output contains one data row per audit log', function (): void {
    $log1 = makeCsvAuditLog(['action' => 'grade_updated']);
    $log2 = makeCsvAuditLog(['action' => 'submission_flagged']);

    $logs     = AuditLog::with('actor')->whereIn('id', [$log1->id, $log2->id])->get();
    $response = (new ExportAuditLogsToCsv())($logs);
    $csv      = captureCsv($response);
    $lines    = array_values(array_filter(explode("\n", trim($csv))));

    // 1 header + 2 data rows
    expect(count($lines))->toBe(3);
});

test('CSV uses System for logs with no actor', function (): void {
    $log = makeCsvAuditLog();

    $logs     = AuditLog::with('actor')->where('id', $log->id)->get();
    $response = (new ExportAuditLogsToCsv())($logs);
    $csv      = captureCsv($response);

    expect($csv)->toContain('System');
});

test('CSV includes actor name and email when actor is present', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $log   = makeCsvAuditLog(['actor_id' => $admin->id]);

    $logs     = AuditLog::with('actor')->where('id', $log->id)->get();
    $response = (new ExportAuditLogsToCsv())($logs);
    $csv      = captureCsv($response);

    expect($csv)->toContain($admin->name);
    expect($csv)->toContain($admin->email);
});
