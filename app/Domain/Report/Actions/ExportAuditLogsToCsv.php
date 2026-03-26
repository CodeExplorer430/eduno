<?php

declare(strict_types=1);

namespace App\Domain\Report\Actions;

use App\Domain\Audit\Models\AuditLog;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportAuditLogsToCsv
{
    /**
     * @param  Collection<int, AuditLog>  $logs
     */
    public function __invoke(Collection $logs): StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="audit-logs.csv"',
        ];

        return response()->stream(function () use ($logs): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['ID', 'Actor', 'Actor Email', 'Action', 'Entity Type', 'Entity ID', 'Date']);

            foreach ($logs as $log) {
                $actor = $log->actor;
                fputcsv($handle, [
                    $log->id,
                    $actor !== null ? $actor->name : 'System',
                    $actor !== null ? $actor->email : '',
                    $log->action,
                    $log->entity_type ?? '',
                    $log->entity_id ?? '',
                    (string) $log->created_at,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
