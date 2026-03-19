<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Course\Models\Course;
use App\Enums\CourseStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('???###')),
            'title' => fake()->sentence(4, false),
            'description' => fake()->optional()->paragraph(),
            'department' => fake()->randomElement(['CCS', 'CBAA', 'COED', 'CAS', 'CE']),
            'term' => fake()->randomElement(['1st Semester', '2nd Semester', 'Summer']),
            'academic_year' => '2025-2026',
            'status' => CourseStatus::Draft,
            'created_by' => User::factory()->state(['role' => 'instructor']),
        ];
    }

    public function published(): static
    {
        return $this->state(['status' => CourseStatus::Published]);
    }
}
