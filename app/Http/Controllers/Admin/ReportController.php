<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Grade\Models\Grade;
use App\Domain\Report\Actions\ExportSubmissionsAsCsv;
use App\Domain\Submission\Models\Submission;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->isAdmin(), 403);
        $this->authorize('viewAny', User::class);

        return Inertia::render('Admin/Reports/Index', [
            'stats' => [
                'total_submissions' => Submission::count(),
                'late_submissions' => Submission::where('is_late', true)->count(),
                'graded' => Grade::count(),
                'released_grades' => Grade::whereNotNull('released_at')->count(),
            ],
        ]);
    }

    public function export(Request $request, ExportSubmissionsAsCsv $action): StreamedResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        $this->authorize('viewAny', User::class);

        return $action();
    }
}
