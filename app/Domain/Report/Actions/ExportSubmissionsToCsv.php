<?php

declare(strict_types=1);

namespace App\Domain\Report\Actions;

use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportSubmissionsToCsv
{
    /**
     * @param  Collection<int, Submission>  $submissions
     */
    public function __invoke(Collection $submissions): StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="submissions.csv"',
        ];

        return response()->stream(function () use ($submissions): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['ID', 'Student', 'Assignment', 'Submitted At', 'Is Late', 'Score', 'Released']);

            foreach ($submissions as $sub) {
                $grade = $sub->grade instanceof Grade ? $sub->grade : null;

                fputcsv($handle, [
                    $sub->id,
                    $sub->student->name ?? '',
                    $sub->assignment->title ?? '',
                    (string) $sub->submitted_at,
                    $sub->is_late ? 'Yes' : 'No',
                    $grade !== null ? (string) $grade->score : '',
                    $grade?->released_at !== null ? (string) $grade->released_at : '',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
