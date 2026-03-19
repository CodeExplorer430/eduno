<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Announcement\Models\Announcement;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Module\Models\Lesson;
use App\Domain\Module\Models\Module;
use App\Domain\Submission\Models\Submission;
use App\Enums\CourseStatus;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $instructors = User::where('role', UserRole::Instructor->value)->get();
        $students = User::where('role', UserRole::Student->value)->get();

        if ($instructors->isEmpty() || $students->isEmpty()) {
            return;
        }

        $coursesData = [
            ['code' => 'CCS101', 'title' => 'Introduction to Computing', 'department' => 'CCS'],
            ['code' => 'CCS201', 'title' => 'Data Structures and Algorithms', 'department' => 'CCS'],
            ['code' => 'CCS301', 'title' => 'Web Development', 'department' => 'CCS'],
        ];

        foreach ($coursesData as $index => $courseData) {
            $instructor = $instructors[$index % $instructors->count()];

            $course = Course::create(array_merge($courseData, [
                'term' => '1st Semester',
                'academic_year' => '2025-2026',
                'status' => CourseStatus::Published,
                'created_by' => $instructor->id,
            ]));

            for ($s = 1; $s <= 2; $s++) {
                $section = CourseSection::create([
                    'course_id' => $course->id,
                    'section_name' => 'Section '.chr(64 + $s),
                    'instructor_id' => $instructor->id,
                    'schedule_text' => $s === 1 ? 'MWF 9:00-10:00' : 'TTh 10:30-12:00',
                ]);

                // Enroll 5 students per section
                $sectionStudents = $students->random(min(5, $students->count()));
                foreach ($sectionStudents as $student) {
                    Enrollment::firstOrCreate(
                        ['user_id' => $student->id, 'course_section_id' => $section->id],
                        ['status' => 'active', 'enrolled_at' => now()]
                    );
                }

                // Create 2 modules per section
                for ($m = 1; $m <= 2; $m++) {
                    $module = Module::create([
                        'course_section_id' => $section->id,
                        'title' => "Module {$m}: ".($m === 1 ? 'Getting Started' : 'Core Concepts'),
                        'order_no' => $m,
                        'published_at' => now()->subDays(7),
                    ]);

                    // 2 lessons per module
                    for ($l = 1; $l <= 2; $l++) {
                        Lesson::create([
                            'module_id' => $module->id,
                            'title' => "Lesson {$l}",
                            'content' => "Content for lesson {$l} of module {$m}.",
                            'type' => 'text',
                            'order_no' => $l,
                            'published_at' => now()->subDays(5),
                        ]);
                    }
                }

                // 2 assignments per section
                for ($a = 1; $a <= 2; $a++) {
                    $assignment = Assignment::create([
                        'course_section_id' => $section->id,
                        'title' => "Assignment {$a}",
                        'instructions' => "Complete the tasks described in assignment {$a}.",
                        'max_score' => 100,
                        'allow_resubmission' => false,
                        'due_at' => now()->addDays(14 * $a),
                        'published_at' => now()->subDay(),
                    ]);

                    // Each student submits
                    foreach ($sectionStudents as $student) {
                        Submission::create([
                            'assignment_id' => $assignment->id,
                            'student_id' => $student->id,
                            'status' => SubmissionStatus::Submitted,
                            'submitted_at' => now()->subHours(rand(1, 48)),
                            'is_late' => false,
                            'attempt_no' => 1,
                        ]);
                    }
                }

                // 1 announcement per section
                Announcement::create([
                    'course_section_id' => $section->id,
                    'title' => 'Welcome to '.$course->title,
                    'body' => 'Welcome to '.$section->section_name.'. Please review the course materials and reach out if you have questions.',
                    'created_by' => $instructor->id,
                    'published_at' => now()->subDays(10),
                ]);
            }
        }
    }
}
