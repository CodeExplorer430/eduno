<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Submission\Models\Submission;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendDeadlineReminders implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $hours = (int) Setting::get('deadline_reminder_hours', 24);

        $assignments = Assignment::query()
            ->whereBetween('due_at', [now(), now()->addHours($hours)])
            ->whereNotNull('published_at')
            ->with('section.enrollments')
            ->get();

        foreach ($assignments as $assignment) {
            $enrolledStudentIds = $assignment->section->enrollments()
                ->where('status', 'active')
                ->pluck('user_id');

            $submittedStudentIds = Submission::where('assignment_id', $assignment->id)
                ->pluck('student_id');

            $unsubmittedStudentIds = $enrolledStudentIds->diff($submittedStudentIds);

            $students = User::whereIn('id', $unsubmittedStudentIds)
                ->with('preferences')
                ->get();

            foreach ($students as $student) {
                if ($student->preferences?->email_notifications === false) {
                    continue;
                }

                SendDeadlineReminder::dispatch($student, $assignment);
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendDeadlineReminders job failed', [
            'message' => $exception->getMessage(),
        ]);
    }
}
