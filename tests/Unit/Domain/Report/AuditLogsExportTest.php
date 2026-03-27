<?php

declare(strict_types=1);

use App\Domain\Audit\Models\AuditLog;
use App\Domain\Report\Actions\AuditLogsExport;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeAuditLogFixture(array $attributes = []): AuditLog
{
    return AuditLog::create(array_merge([
        'action'      => 'grade_updated',
        'entity_type' => 'Grade',
        'entity_id'   => 1,
        'actor_id'    => null,
    ], $attributes));
}

test('headings returns 7 column headers', function (): void {
    $export = new AuditLogsExport(collect());

    expect($export->headings())->toBe([
        'ID', 'Actor', 'Actor Email', 'Action', 'Entity Type', 'Entity ID', 'Date',
    ]);
});

test('map returns System for null actor', function (): void {
    $log = makeAuditLogFixture();
    $log->load('actor');

    $export = new AuditLogsExport(collect([$log]));
    $row    = $export->map($log);

    expect($row[1])->toBe('System');
    expect($row[2])->toBe('');
});

test('map uses actor name and email when actor is present', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $log   = makeAuditLogFixture(['actor_id' => $admin->id]);
    $log->load('actor');

    $export = new AuditLogsExport(collect([$log]));
    $row    = $export->map($log);

    expect($row[1])->toBe($admin->name);
    expect($row[2])->toBe($admin->email);
});

test('map sanitizes action starting with equals sign by prepending a tab', function (): void {
    $log = makeAuditLogFixture(['action' => '=cmd']);
    $log->load('actor');

    $export = new AuditLogsExport(collect([$log]));
    $row    = $export->map($log);

    expect($row[3])->toStartWith("\t");
    expect($row[3])->toContain('=cmd');
});

test('map sanitizes action starting with minus sign by prepending a tab', function (): void {
    $log = makeAuditLogFixture(['action' => '-2+3']);
    $log->load('actor');

    $export = new AuditLogsExport(collect([$log]));
    $row    = $export->map($log);

    expect($row[3])->toStartWith("\t");
});

test('map does not modify safe action values', function (): void {
    $log = makeAuditLogFixture(['action' => 'grade_updated']);
    $log->load('actor');

    $export = new AuditLogsExport(collect([$log]));
    $row    = $export->map($log);

    expect($row[3])->toBe('grade_updated');
});

test('styles applies bold white header on dark blue background', function (): void {
    $export = new AuditLogsExport(collect());

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet       = $spreadsheet->getActiveSheet();
    $export->styles($sheet);

    $style = $sheet->getStyle('A1:G1');
    expect($style->getFont()->getBold())->toBeTrue();
    expect(strtoupper($style->getFill()->getStartColor()->getRGB()))->toBe('1E3A5F');
});
