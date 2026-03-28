<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Audit\Models\AuditLog;
use App\Domain\Report\Actions\AuditLogsExport;
use App\Domain\Report\Actions\ExportAuditLogsToCsv;
use App\Domain\Report\Actions\ExportAuditLogsToPdf;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', User::class);

        $logs = $this->buildQuery($request)->paginate(20)->withQueryString();

        return Inertia::render('Admin/AuditLogs/Index', [
            'logs'    => $logs,
            'filters' => $request->only(['action', 'actor_email', 'from', 'to']),
        ]);
    }

    public function export(Request $request): StreamedResponse|BinaryFileResponse|HttpResponse
    {
        $this->authorize('viewAny', User::class);

        $format = $request->query('format', 'csv');

        /** @var \Illuminate\Database\Eloquent\Collection<int, AuditLog> $logs */
        $logs = $this->buildQuery($request)->get();

        return match ($format) {
            'xlsx'  => Excel::download(new AuditLogsExport($logs), 'audit-logs.xlsx'),
            'pdf'   => (new ExportAuditLogsToPdf())($logs),
            default => (new ExportAuditLogsToCsv())($logs),
        };
    }

    private function buildQuery(Request $request): Builder
    {
        $query = AuditLog::with('actor')->orderByDesc('created_at');

        if ($request->filled('action')) {
            $query->where('action', 'like', '%'.$request->query('action').'%');
        }

        if ($request->filled('actor_email')) {
            $query->whereHas('actor', function (Builder $q) use ($request): void {
                $q->where('email', 'like', '%'.$request->query('actor_email').'%');
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->query('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->query('to'));
        }

        return $query;
    }
}
