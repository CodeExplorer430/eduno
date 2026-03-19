<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Announcement\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        return [
            'course_section_id' => CourseSectionFactory::new(),
            'title' => fake()->sentence(5, false),
            'body' => fake()->paragraphs(2, true),
            'created_by' => User::factory()->state(['role' => 'instructor']),
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(['published_at' => now()->subMinute()]);
    }
}
