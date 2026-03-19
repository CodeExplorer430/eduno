<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Submission\Models\Submission;
use App\Enums\SubmissionStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    protected $model = Submission::class;

    public function definition(): array
    {
        return [
            'assignment_id' => AssignmentFactory::new(),
            'student_id' => User::factory()->state(['role' => 'student']),
            'status' => SubmissionStatus::Submitted,
            'submitted_at' => now(),
            'is_late' => false,
            'attempt_no' => 1,
        ];
    }
}
