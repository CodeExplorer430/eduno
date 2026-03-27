<?php

declare(strict_types=1);

namespace App\Domain\Report\Actions;

use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class ExportSubmissionsToPdf
{
    /**
     * @param  Collection<int, Submission>  $submissions
     */
    public function __invoke(Collection $submissions): Response
    {
        $rows = $submissions->map(function (Submission $sub): array {
            $grade = $sub->grade instanceof Grade ? $sub->grade : null;

            return [
                'id'           => $sub->id,
                'student'      => $sub->student->name ?? '',
                'assignment'   => $sub->assignment->title ?? '',
                'submitted_at' => (string) $sub->submitted_at,
                'is_late'      => $sub->is_late ? 'Yes' : 'No',
                'score'        => $grade !== null ? (string) $grade->score : '',
                'released'     => $grade?->released_at !== null ? (string) $grade->released_at : '',
            ];
        });

        $pdf = Pdf::loadView('exports.submissions-pdf', ['rows' => $rows]);

        return $pdf->download('submissions.pdf');
    }
}
