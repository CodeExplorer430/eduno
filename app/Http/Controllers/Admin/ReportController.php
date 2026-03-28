<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Report\Actions\ExportSubmissionsToCsv;
use App\Domain\Report\Actions\ExportSubmissionsToPdf;
use App\Domain\Report\Actions\GetAdminAnalytics;
use App\Domain\Report\Actions\GetAdminReport;
use App\Domain\Report\Actions\SubmissionsExport;
use App\Domain\Submission\Models\Submission;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request, GetAdminReport $action, GetAdminAnalytics $analytics): Response
    {
        $this->authorize('viewAny', User::class);

        $report = $action->handle();

        return Inertia::render('Admin/Reports/Index', [
            'stats' => [
                'total_submissions' => $report['total_submissions'],
                'late_submissions'  => $report['late_submissions'],
                'graded'            => $report['graded_submissions'],
                'released_grades'   => $report['released_grades_count'],
            ],
            'charts' => $analytics->handle(),
        ]);
    }

    public function export(Request $request): StreamedResponse|BinaryFileResponse|HttpResponse
    {
        $this->authorize('viewAny', User::class);

        $format = $request->query('format', 'csv');

        /** @var \Illuminate\Database\Eloquent\Collection<int, Submission> $submissions */
        $submissions = Submission::with(['student', 'assignment', 'grade'])
            ->orderBy('submitted_at')
            ->get();

        return match ($format) {
            'xlsx'  => Excel::download(new SubmissionsExport($submissions), 'submissions.xlsx'),
            'pdf'   => (new ExportSubmissionsToPdf())($submissions),
            default => (new ExportSubmissionsToCsv())($submissions),
        };
    }
}
