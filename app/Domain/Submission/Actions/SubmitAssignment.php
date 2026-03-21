<?php

declare(strict_types=1);

namespace App\Domain\Submission\Actions;

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Submission\Models\Submission;
use App\Domain\Submission\Models\SubmissionFile;
use App\Enums\SubmissionStatus;
use App\Models\User;
use App\Notifications\NewSubmissionNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubmitAssignment
{
    /**
     * @param  array<int, UploadedFile>  $files
     */
    public function handle(Assignment $assignment, User $student, array $files): Submission
    {
        $submission = DB::transaction(function () use ($assignment, $student, $files): Submission {
            $isLate = $assignment->due_at !== null && now()->isAfter($assignment->due_at);

            $attemptNo = Submission::where('assignment_id', $assignment->id)
                ->where('student_id', $student->id)
                ->count() + 1;

            $submission = Submission::create([
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
                'status' => SubmissionStatus::Submitted,
                'submitted_at' => now(),
                'is_late' => $isLate,
                'attempt_no' => $attemptNo,
            ]);

            foreach ($files as $file) {
                $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
                $path = "submissions/{$assignment->id}/{$submission->id}";
                $storedPath = $file->storeAs($path, $filename, 'private');

                SubmissionFile::create([
                    'submission_id' => $submission->id,
                    'file_path' => $storedPath,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType() ?? $file->getClientMimeType(),
                    'size_bytes' => $file->getSize(),
                ]);
            }

            return $submission->load('files');
        });

        $submission->load(['assignment.section.instructor', 'student']);

        $instructor = $submission->assignment->section->instructor;
        if ($instructor instanceof User) {
            $instructor->notify(new NewSubmissionNotification($submission));
        }

        return $submission;
    }
}
