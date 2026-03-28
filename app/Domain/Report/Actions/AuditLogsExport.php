<?php

declare(strict_types=1);

namespace App\Domain\Report\Actions;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * @implements WithMapping<\App\Domain\Audit\Models\AuditLog>
 */
class AuditLogsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /** @var \Illuminate\Support\Collection<int, \App\Domain\Audit\Models\AuditLog> */
    private \Illuminate\Support\Collection $logs;

    /**
     * @param \Illuminate\Support\Collection<int, \App\Domain\Audit\Models\AuditLog> $logs
     */
    public function __construct(\Illuminate\Support\Collection $logs)
    {
        $this->logs = $logs;
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Domain\Audit\Models\AuditLog>
     */
    public function collection(): \Illuminate\Support\Collection
    {
        return $this->logs;
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['ID', 'Actor', 'Actor Email', 'Action', 'Entity Type', 'Entity ID', 'Date'];
    }

    /**
     * @param \App\Domain\Audit\Models\AuditLog $row
     * @return array<int, mixed>
     */
    public function map($row): array
    {
        $actor = $row->actor;

        return [
            $row->id,
            $actor !== null ? $actor->name : 'System',
            $actor !== null ? $actor->email : '',
            $this->sanitizeExcelCell($row->action),
            $row->entity_type ?? '',
            $row->entity_id ?? '',
            (string) $row->created_at,
        ];
    }

    /**
     * Prevent formula injection (OWASP A03): prefix values that start with a
     * formula-trigger character so Excel treats the cell as plain text.
     */
    private function sanitizeExcelCell(string $value): string
    {
        if ($value !== '' && preg_match('/^[=+\-@|:]/', $value)) {
            return "\t" . $value;
        }

        return $value;
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '1e3a5f']],
        ]);
    }
}
