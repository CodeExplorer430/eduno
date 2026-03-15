<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Submission\Models\Grade;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Grade>
 */
class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public function definition(): array
    {
        return [
            'submission_id' => SubmissionFactory::new(),
            'graded_by' => User::factory()->state(['role' => 'instructor']),
            'score' => 85.00,
            'feedback' => fake()->optional()->sentence(),
            'released_at' => null,
        ];
    }

    public function released(): static
    {
        return $this->state(['released_at' => now()]);
    }
}
