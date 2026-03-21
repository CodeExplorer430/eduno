<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Assignment\Models\Assignment;
use App\Jobs\SendDeadlineReminder;
use Illuminate\Console\Command;

class SendDailyDeadlineReminders extends Command
{
    protected $signature = 'reminders:deadlines';

    protected $description = 'Send deadline reminder emails to students with upcoming due dates';

    public function handle(): int
    {
        $tomorrow = now()->addDay();

        $assignments = Assignment::whereNotNull('due_at')
            ->whereBetween('due_at', [now(), $tomorrow->endOfDay()])
            ->whereNotNull('published_at')
            ->with('courseSection.enrollments.user')
            ->get();

        $count = 0;

        foreach ($assignments as $assignment) {
            foreach ($assignment->courseSection->enrollments as $enrollment) {
                $student = $enrollment->user;

                // Skip students who already submitted
                $alreadySubmitted = $assignment->submissions()
                    ->where('student_id', $student->id)
                    ->exists();

                if (! $alreadySubmitted) {
                    SendDeadlineReminder::dispatch($student, $assignment);
                    $count++;
                }
            }
        }

        $this->info("Dispatched {$count} deadline reminder(s).");

        return self::SUCCESS;
    }
}
