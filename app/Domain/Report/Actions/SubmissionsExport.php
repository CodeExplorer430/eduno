<?php

declare(strict_types=1);

namespace App\Domain\Report\Actions;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * @implements WithMapping<\App\Domain\Submission\Models\Submission>
 */
class SubmissionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /** @var \Illuminate\Support\Collection<int, \App\Domain\Submission\Models\Submission> */
    private \Illuminate\Support\Collection $submissions;

    /**
     * @param \Illuminate\Support\Collection<int, \App\Domain\Submission\Models\Submission> $submissions
     */
    public function __construct(\Illuminate\Support\Collection $submissions)
    {
        $this->submissions = $submissions;
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Domain\Submission\Models\Submission>
     */
    public function collection(): \Illuminate\Support\Collection
    {
        return $this->submissions;
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['ID', 'Student', 'Assignment', 'Submitted At', 'Is Late', 'Score', 'Released'];
    }

    /**
     * @param \App\Domain\Submission\Models\Submission $row
     * @return array<int, mixed>
     */
    public function map($row): array
    {
        $grade = $row->grade instanceof \App\Domain\Submission\Models\Grade ? $row->grade : null;

        return [
            $row->id,
            $this->sanitizeExcelCell($row->student->name ?? ''),
            $this->sanitizeExcelCell($row->assignment->title ?? ''),
            (string) $row->submitted_at,
            $row->is_late ? 'Yes' : 'No',
            $grade !== null ? (string) $grade->score : '',
            $grade?->released_at !== null ? (string) $grade->released_at : '',
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
