<?php

declare(strict_types=1);

namespace App\Domain\Report\Actions;

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Submission\Models\Submission;
use Illuminate\Database\Eloquent\Collection;

class ExportSubmissions
{
    public function handle(Assignment $assignment): string
    {
        /** @var Collection<int, Submission> $submissions */
        $submissions = $assignment->submissions()->with(['student', 'grade'])->get();

        $rows = [];
        $rows[] = implode(',', ['student_name', 'student_email', 'submitted_at', 'is_late', 'attempt_no', 'score', 'feedback']);

        foreach ($submissions as $submission) {
            $rows[] = implode(',', [
                $this->escapeCsv($submission->student->name ?? ''),
                $this->escapeCsv($submission->student->email ?? ''),
                $this->escapeCsv($submission->submitted_at->toDateTimeString()),
                $submission->is_late ? '1' : '0',
                (string) $submission->attempt_no,
                $submission->grade ? (string) $submission->grade->score : '',
                $this->escapeCsv($submission->grade !== null ? ($submission->grade->feedback ?? '') : ''),
            ]);
        }

        return implode("\n", $rows)."\n";
    }

    private function escapeCsv(string $value): string
    {
        if (str_contains($value, ',') || str_contains($value, '"') || str_contains($value, "\n")) {
            return '"'.str_replace('"', '""', $value).'"';
        }

        return $value;
    }
}
