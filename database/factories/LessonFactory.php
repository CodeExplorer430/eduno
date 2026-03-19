<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Module\Models\Lesson;
use App\Enums\LessonType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition(): array
    {
        static $order = 0;

        return [
            'module_id' => ModuleFactory::new(),
            'title' => fake()->sentence(4, false),
            'content' => fake()->optional()->paragraphs(2, true),
            'type' => LessonType::Text,
            'order_no' => ++$order,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(['published_at' => now()->subMinute()]);
    }
}
