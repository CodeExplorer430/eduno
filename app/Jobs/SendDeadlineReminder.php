<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domain\Assignment\Models\Assignment;
use App\Mail\DeadlineReminderMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendDeadlineReminder implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly User $student,
        public readonly Assignment $assignment,
    ) {
    }

    public function handle(): void
    {
        Mail::to($this->student->email)->send(new DeadlineReminderMail($this->assignment));
    }
}
