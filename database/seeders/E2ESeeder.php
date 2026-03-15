<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Enums\CourseStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class E2ESeeder extends Seeder
{
    public function run(): void
    {
        $student = User::firstOrCreate(
            ['email' => 'student@eduno.test'],
            [
                'name' => 'E2E Student',
                'password' => Hash::make('password'),
                'role' => UserRole::Student,
                'email_verified_at' => now(),
            ]
        );

        $instructor = User::firstOrCreate(
            ['email' => 'instructor@eduno.test'],
            [
                'name' => 'E2E Instructor',
                'password' => Hash::make('password'),
                'role' => UserRole::Instructor,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@eduno.test'],
            [
                'name' => 'E2E Admin',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ]
        );

        $course = Course::firstOrCreate(
            ['code' => 'E2E101'],
            [
                'title' => 'E2E Test Course',
                'department' => 'CCS',
                'term' => '1st Semester',
                'academic_year' => '2025-2026',
                'status' => CourseStatus::Published,
                'created_by' => $instructor->id,
            ]
        );

        $section = CourseSection::firstOrCreate(
            ['course_id' => $course->id, 'section_name' => 'Section A'],
            ['instructor_id' => $instructor->id]
        );

        Enrollment::firstOrCreate(
            ['user_id' => $student->id, 'course_section_id' => $section->id],
            ['status' => 'active', 'enrolled_at' => now()]
        );

        Assignment::firstOrCreate(
            ['course_section_id' => $section->id, 'title' => 'E2E Assignment'],
            [
                'max_score' => 100,
                'allow_resubmission' => false,
                'due_at' => now()->addDays(5),
                'published_at' => now()->subMinute(),
            ]
        );
    }
}
