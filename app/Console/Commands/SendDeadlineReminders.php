<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Assignment\Models\Assignment;
use App\Notifications\DeadlineReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendDeadlineReminders extends Command
{
    protected $signature = 'schedule:deadline-reminders';

    protected $description = 'Send deadline reminder notifications to students with upcoming assignments';

    public function handle(): void
    {
        $assignments = Assignment::query()
            ->whereNotNull('published_at')
            ->whereBetween('due_at', [now(), now()->addHours(24)])
            ->get();

        foreach ($assignments as $assignment) {
            $students = $assignment->section->enrollments()
                ->where('status', 'active')
                ->with('student')
                ->whereDoesntHave('student.submissions', function ($query) use ($assignment): void {
                    $query->where('assignment_id', $assignment->id);
                })
                ->get()
                ->pluck('student');

            Notification::send($students, new DeadlineReminderNotification($assignment));
        }

        $this->info("Deadline reminders sent for {$assignments->count()} assignment(s).");
    }
}
