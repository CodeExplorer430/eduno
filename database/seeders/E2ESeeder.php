<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Announcement\Models\Announcement;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Submission\Models\Submission;
use App\Enums\CourseStatus;
use App\Enums\SubmissionStatus;
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

        // Primary assignment — kept clear so submission.spec.ts can upload and submit
        Assignment::firstOrCreate(
            ['course_section_id' => $section->id, 'title' => 'E2E Assignment'],
            [
                'max_score' => 100,
                'allow_resubmission' => false,
                'due_at' => now()->addDays(5),
                'published_at' => now()->subMinute(),
            ]
        );

        // Dedicated assignment with a pre-seeded submission for grading.spec.ts
        $gradingAssignment = Assignment::firstOrCreate(
            ['course_section_id' => $section->id, 'title' => 'E2E Grading Assignment'],
            [
                'max_score' => 50,
                'allow_resubmission' => false,
                'due_at' => now()->addDays(10),
                'published_at' => now()->subMinutes(2),
            ]
        );

        Submission::firstOrCreate(
            ['assignment_id' => $gradingAssignment->id, 'student_id' => $student->id],
            [
                'status' => SubmissionStatus::Submitted,
                'submitted_at' => now()->subHour(),
                'is_late' => false,
                'attempt_no' => 1,
            ]
        );

        // Announcement visible in student dashboard Recent Announcements
        Announcement::firstOrCreate(
            ['course_section_id' => $section->id, 'title' => 'E2E Announcement'],
            [
                'body' => 'Welcome to E2E Test Course. This is a test announcement.',
                'created_by' => $instructor->id,
                'published_at' => now()->subMinute(),
            ]
        );
    }
}
