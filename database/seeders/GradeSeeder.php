<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $instructors = User::where('role', UserRole::Instructor->value)->get();

        if ($instructors->isEmpty()) {
            return;
        }

        // Grade and release all submissions from the first assignment in each section
        // (simulates one graded assignment cycle per section)
        $submissions = Submission::with(['assignment.section'])
            ->get()
            ->groupBy('assignment_id');

        foreach ($submissions as $assignmentId => $assignmentSubmissions) {
            $firstGroup = $assignmentSubmissions->first();
            $instructor = $firstGroup->assignment->section->instructor_id
                ? User::find($firstGroup->assignment->section->instructor_id)
                : $instructors->first();

            if (! $instructor) {
                continue;
            }

            // Grade the first assignment per section (released), leave second ungraded
            /** @var \App\Domain\Assignment\Models\Assignment $assignment */
            $assignment = $firstGroup->assignment;
            /** @var \App\Domain\Assignment\Models\Assignment|null $firstSectionAssignment */
            $firstSectionAssignment = $assignment->section->assignments()->orderBy('id')->first();
            $isFirstAssignment = $firstSectionAssignment?->id === $assignment->id;

            foreach ($assignmentSubmissions as $submission) {
                $score = (float) rand(60, 100);
                $released = $isFirstAssignment;

                $grade = Grade::create([
                    'submission_id' => $submission->id,
                    'graded_by' => $instructor->id,
                    'score' => $score,
                    'feedback' => $this->feedbackFor($score),
                    'released_at' => $released ? now()->subDays(3) : null,
                ]);

                $submission->update([
                    'status' => $released ? SubmissionStatus::Returned : SubmissionStatus::Graded,
                ]);

                DB::table('audit_logs')->insert([
                    'actor_id' => $instructor->id,
                    'action' => 'grade.created',
                    'entity_type' => Grade::class,
                    'entity_id' => $grade->id,
                    'metadata' => json_encode([
                        'score' => $grade->score,
                        'submission_id' => $submission->id,
                    ]),
                    'created_at' => now()->subDays(4),
                ]);

                if ($released) {
                    DB::table('audit_logs')->insert([
                        'actor_id' => $instructor->id,
                        'action' => 'grade.released',
                        'entity_type' => Grade::class,
                        'entity_id' => $grade->id,
                        'metadata' => json_encode([
                            'score' => $grade->score,
                            'submission_id' => $submission->id,
                        ]),
                        'created_at' => now()->subDays(3),
                    ]);
                }
            }
        }
    }

    private function feedbackFor(float $score): string
    {
        return match (true) {
            $score >= 90 => 'Excellent work! You demonstrated a thorough understanding of the concepts.',
            $score >= 80 => 'Good job. A few areas could use more detail, but overall well done.',
            $score >= 70 => 'Satisfactory submission. Please review the feedback areas for improvement.',
            default => 'Needs improvement. Please revisit the course materials and resubmit if allowed.',
        };
    }
}
