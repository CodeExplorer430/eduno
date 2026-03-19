<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Report\Actions\ExportSubmissions;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionExportController extends Controller
{
    public function __invoke(Assignment $assignment, ExportSubmissions $action): StreamedResponse
    {
        $this->authorize('export', $assignment);

        return response()->streamDownload(
            fn () => print ($action->handle($assignment)),
            "submissions-{$assignment->id}.csv",
            ['Content-Type' => 'text/csv'],
        );
    }
}
