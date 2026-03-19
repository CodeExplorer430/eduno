<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Course\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'student']),
            'course_section_id' => CourseSectionFactory::new(),
            'status' => 'active',
            'enrolled_at' => now(),
        ];
    }
}
