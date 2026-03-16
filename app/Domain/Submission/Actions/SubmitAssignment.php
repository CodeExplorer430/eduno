<?php

declare(strict_types=1);

namespace App\Domain\Submission\Actions;

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Submission\Models\Submission;
use App\Domain\Submission\Models\SubmissionFile;
use App\Jobs\NotifyInstructorNewSubmission;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubmitAssignment
{
    /** @param array<UploadedFile> $files */
    public function execute(User $student, Assignment $assignment, array $files): Submission
    {
        return DB::transaction(function () use ($student, $assignment, $files): Submission {
            $isLate = $assignment->due_at !== null && now()->isAfter($assignment->due_at);

            $previousAttempts = Submission::where('assignment_id', $assignment->id)
                ->where('student_id', $student->id)
                ->count();

            $submission = Submission::create([
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
                'status' => 'submitted',
                'is_late' => $isLate,
                'attempt_no' => $previousAttempts + 1,
            ]);

            foreach ($files as $file) {
                $uuid = (string) Str::uuid();
                $ext = $file->getClientOriginalExtension();
                $path = $file->storeAs('submissions', "{$uuid}.{$ext}", 'private');

                SubmissionFile::create([
                    'submission_id' => $submission->id,
                    'file_path' => (string) $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType() ?? $file->getClientMimeType(),
                    'size_bytes' => $file->getSize(),
                ]);
            }

            $submission->load('files');

            NotifyInstructorNewSubmission::dispatch($submission);

            return $submission;
        });
    }
}
