<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domain\Grade\Models\Grade;
use App\Mail\GradeReleasedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class NotifyStudentGradeReleased implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Grade $grade) {}

    public function handle(): void
    {
        $student = $this->grade->submission->student;

        Mail::to($student->email)->send(new GradeReleasedMail($this->grade));
    }
}
