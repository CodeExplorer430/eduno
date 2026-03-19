<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Course\Models\CourseSection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseSection>
 */
class CourseSectionFactory extends Factory
{
    protected $model = CourseSection::class;

    public function definition(): array
    {
        return [
            'course_id' => CourseFactory::new(),
            'section_name' => 'Section '.fake()->randomLetter(),
            'instructor_id' => User::factory()->state(['role' => 'instructor']),
            'schedule_text' => fake()->optional()->randomElement([
                'MWF 8:00-9:00',
                'TTh 10:30-12:00',
                'Sat 9:00-12:00',
            ]),
        ];
    }
}
