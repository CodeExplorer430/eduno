<?php

declare(strict_types=1);

use App\Domain\Report\Actions\GetAdminReport;

test('returns array with all expected keys', function (): void {
    $report = (new GetAdminReport())->handle();

    expect($report)->toHaveKeys([
        'total_courses',
        'total_sections',
        'total_students',
        'total_submissions',
        'late_submissions',
        'graded_submissions',
    ]);
});

test('all values are integers', function (): void {
    $report = (new GetAdminReport())->handle();

    foreach ($report as $value) {
        expect($value)->toBeInt();
    }
});
