<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Assignment\Models\Assignment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assignment>
 */
class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition(): array
    {
        return [
            'course_section_id' => CourseSectionFactory::new(),
            'title' => fake()->sentence(4, false),
            'instructions' => fake()->optional()->paragraph(),
            'max_score' => 100,
            'allow_resubmission' => false,
            'due_at' => now()->addDays(7),
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(['published_at' => now()->subMinute()]);
    }
}
