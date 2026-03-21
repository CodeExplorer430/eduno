<?php

declare(strict_types=1);

namespace App\Domain\Report\Actions;

use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportSubmissionsAsCsv
{
    public function __invoke(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="submissions-export.csv"',
        ];

        $callback = function (): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['ID', 'Student', 'Assignment', 'Submitted At', 'Is Late', 'Score', 'Released']);

            /** @var Builder<Submission> $query */
            $query = Submission::with(['student', 'assignment', 'grade'])->orderBy('submitted_at');

            $query->chunk(200, function (Collection $submissions) use ($handle): void {
                /** @var Submission $sub */
                foreach ($submissions as $sub) {
                    $grade = $sub->grade instanceof Grade ? $sub->grade : null;

                    fputcsv($handle, [
                        $sub->id,
                        $sub->student->name,
                        $sub->assignment->title,
                        (string) $sub->submitted_at,
                        $sub->is_late ? 'Yes' : 'No',
                        $grade !== null ? (string) $grade->score : '',
                        $grade !== null && $grade->released_at !== null ? (string) $grade->released_at : '',
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
