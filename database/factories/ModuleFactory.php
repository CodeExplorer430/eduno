<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Module\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Module>
 */
class ModuleFactory extends Factory
{
    protected $model = Module::class;

    public function definition(): array
    {
        static $order = 0;

        return [
            'course_section_id' => CourseSectionFactory::new(),
            'title' => fake()->sentence(3, false),
            'description' => fake()->optional()->paragraph(),
            'order_no' => ++$order,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(['published_at' => now()->subMinute()]);
    }
}
