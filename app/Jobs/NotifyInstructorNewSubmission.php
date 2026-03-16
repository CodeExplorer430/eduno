<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domain\Submission\Models\Submission;
use App\Mail\NewSubmissionMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class NotifyInstructorNewSubmission implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Submission $submission) {}

    public function handle(): void
    {
        $instructor = $this->submission->assignment->courseSection->instructor;

        Mail::to($instructor->email)->send(new NewSubmissionMail($this->submission));
    }
}
