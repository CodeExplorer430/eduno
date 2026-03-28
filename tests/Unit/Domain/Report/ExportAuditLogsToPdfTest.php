<?php

declare(strict_types=1);

use App\Domain\Audit\Models\AuditLog;
use App\Domain\Report\Actions\ExportAuditLogsToPdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

uses(RefreshDatabase::class);

test('response is an HTTP response with pdf content-type', function (): void {
    $response = (new ExportAuditLogsToPdf())(collect());

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->headers->get('Content-Type'))->toContain('application/pdf');
});

test('response has content-disposition attachment with audit-logs filename', function (): void {
    $response = (new ExportAuditLogsToPdf())(collect());

    expect($response->headers->get('Content-Disposition'))->toContain('audit-logs.pdf');
});

test('generates PDF from logs with null actor without throwing', function (): void {
    $log = AuditLog::create([
        'action'      => 'grade_updated',
        'entity_type' => 'Grade',
        'entity_id'   => 1,
        'actor_id'    => null,
    ]);

    $logs     = AuditLog::with('actor')->where('id', $log->id)->get();
    $response = (new ExportAuditLogsToPdf())($logs);

    expect($response->headers->get('Content-Type'))->toContain('application/pdf');
});
