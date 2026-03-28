<?php

declare(strict_types=1);

namespace App\Domain\Report\Actions;

use App\Domain\Audit\Models\AuditLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class ExportAuditLogsToPdf
{
    /**
     * @param  Collection<int, AuditLog>  $logs
     */
    public function __invoke(Collection $logs): Response
    {
        $rows = $logs->map(function (AuditLog $log): array {
            $actor = $log->actor;

            return [
                'id'     => $log->id,
                'actor'  => $actor !== null ? $actor->name : 'System',
                'action' => $log->action,
                'entity' => ($log->entity_type ?? '').' #'.($log->entity_id ?? ''),
                'date'   => (string) $log->created_at,
            ];
        });

        $pdf = Pdf::loadView('exports.audit-logs-pdf', ['rows' => $rows]);

        return $pdf->download('audit-logs.pdf');
    }
}
