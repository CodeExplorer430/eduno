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
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $instructors = User::where('role', UserRole::Instructor->value)->get();
        $students = User::where('role', UserRole::Student->value)->get();

        if ($instructors->isEmpty() || $students->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($instructors, $students): void {
            $this->seed($instructors, $students);
        });
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Collection<int, User>  $instructors
     * @param  \Illuminate\Database\Eloquent\Collection<int, User>  $students
     */
    private function seed(\Illuminate\Database\Eloquent\Collection $instructors, \Illuminate\Database\Eloquent\Collection $students): void
    {

        $coursesData = [
            // 1st Year — 1st Semester
            ['code' => 'CC109',    'title' => 'Business Application Software',                      'department' => 'CCS',  'term' => '1st Semester'],
            ['code' => 'CCS101',   'title' => 'Introduction to Computing',                          'department' => 'CCS',  'term' => '1st Semester'],
            ['code' => 'CCS102',   'title' => 'Computer Programming 1',                             'department' => 'CCS',  'term' => '1st Semester'],
            ['code' => 'GEC001',   'title' => 'Understanding the Self',                             'department' => 'GED',  'term' => '1st Semester'],
            ['code' => 'GEC002',   'title' => 'Readings in Philippine History',                     'department' => 'GED',  'term' => '1st Semester'],
            ['code' => 'GEC004',   'title' => 'Mathematics in the Modern World',                    'department' => 'GED',  'term' => '1st Semester'],
            ['code' => 'NSTP111',  'title' => 'Civic Welfare Training Services 1',                  'department' => 'NSTP', 'term' => '1st Semester'],
            ['code' => 'PATHFIT1', 'title' => 'Movement Competency Training',                       'department' => 'PE',   'term' => '1st Semester'],
            // 1st Year — 2nd Semester
            ['code' => 'CCS103',   'title' => 'Computer Programming 2',                             'department' => 'CCS',  'term' => '2nd Semester'],
            ['code' => 'CCS107',   'title' => 'Web Systems and Technologies',                       'department' => 'CCS',  'term' => '2nd Semester'],
            ['code' => 'CCS108',   'title' => 'Technical Computer Concept',                         'department' => 'CCS',  'term' => '2nd Semester'],
            ['code' => 'GEC003',   'title' => 'The Contemporary World',                             'department' => 'GED',  'term' => '2nd Semester'],
            ['code' => 'NSTP122',  'title' => 'Civic Welfare Training Services 2',                  'department' => 'NSTP', 'term' => '2nd Semester'],
            ['code' => 'PATHFIT2', 'title' => 'Exercise-Based Fitness Activities',                  'department' => 'PE',   'term' => '2nd Semester'],
            ['code' => 'PR001',    'title' => 'College Algebra',                                    'department' => 'SCI',  'term' => '2nd Semester'],
            // 2nd Year — 1st Semester
            ['code' => 'CCS104',   'title' => 'Data Structures and Algorithms',                     'department' => 'CCS',  'term' => '1st Semester'],
            ['code' => 'CCS105',   'title' => 'Information Management',                             'department' => 'CCS',  'term' => '1st Semester'],
            ['code' => 'CCS110',   'title' => 'Digital Graphics',                                   'department' => 'CCS',  'term' => '1st Semester'],
            ['code' => 'CCS117',   'title' => 'Networking 1',                                       'department' => 'CCS',  'term' => '1st Semester'],
            ['code' => 'CCS124',   'title' => 'Discrete Mathematics',                               'department' => 'CCS',  'term' => '1st Semester'],
            ['code' => 'GEC005',   'title' => 'Purposive Communication',                            'department' => 'GED',  'term' => '1st Semester'],
            ['code' => 'LIT001',   'title' => 'Philippine Literature in English',                   'department' => 'HUM',  'term' => '1st Semester'],
            ['code' => 'PATHFIT3', 'title' => 'Dance and Fitness',                                  'department' => 'PE',   'term' => '1st Semester'],
            // 2nd Year — 2nd Semester
            ['code' => 'CCS106',   'title' => 'Applications Development and Emerging Technologies', 'department' => 'CCS',  'term' => '2nd Semester'],
            ['code' => 'GEC007',   'title' => 'Science, Technology and Society',                    'department' => 'GED',  'term' => '2nd Semester'],
            ['code' => 'IT101',    'title' => 'Integrative Programming and Technologies',           'department' => 'IT',   'term' => '2nd Semester'],
            ['code' => 'IT102',    'title' => 'Advanced Database Systems',                          'department' => 'IT',   'term' => '2nd Semester'],
            ['code' => 'IT103',    'title' => 'Computer System Organization',                       'department' => 'IT',   'term' => '2nd Semester'],
            ['code' => 'IT104',    'title' => 'Networking 2',                                       'department' => 'IT',   'term' => '2nd Semester'],
            ['code' => 'PATHFIT4', 'title' => 'Sports and Fitness',                                 'department' => 'PE',   'term' => '2nd Semester'],
            // 3rd Year — 1st Semester
            ['code' => 'CCS111',   'title' => 'System Analysis and Design',                         'department' => 'CCS',  'term' => '1st Semester'],
            ['code' => 'CCS113',   'title' => 'Information Assurance and Security 1',               'department' => 'CCS',  'term' => '1st Semester'],
            ['code' => 'CCS116',   'title' => 'Web System and Technologies 2',                      'department' => 'CCS',  'term' => '1st Semester'],
            ['code' => 'CCS118',   'title' => 'Multimedia Systems',                                 'department' => 'CCS',  'term' => '1st Semester'],
            ['code' => 'IT105',    'title' => 'IT Major Elective 1',                                'department' => 'IT',   'term' => '1st Semester'],
            ['code' => 'IT106',    'title' => 'Logic Design and Switching',                         'department' => 'IT',   'term' => '1st Semester'],
            ['code' => 'PR002',    'title' => 'Probability and Statistics',                         'department' => 'SCI',  'term' => '1st Semester'],
            // 3rd Year — 2nd Semester
            ['code' => 'CCS112',   'title' => 'Operating System and Application',                   'department' => 'CCS',  'term' => '2nd Semester'],
            ['code' => 'CCS123',   'title' => 'Introduction to Human Computer Interaction',         'department' => 'CCS',  'term' => '2nd Semester'],
            ['code' => 'CCS125',   'title' => 'Software Engineering 1',                             'department' => 'CCS',  'term' => '2nd Semester'],
            ['code' => 'IT107',    'title' => 'System Administration and Maintenance',              'department' => 'IT',   'term' => '2nd Semester'],
            ['code' => 'IT108',    'title' => 'Information Assurance and Security 2',               'department' => 'IT',   'term' => '2nd Semester'],
            ['code' => 'IT109',    'title' => 'IT Major Elective 2',                                'department' => 'IT',   'term' => '2nd Semester'],
            ['code' => 'PR003',    'title' => 'Methods of Research',                                'department' => 'SCI',  'term' => '2nd Semester'],
        ];

        foreach ($coursesData as $index => $courseData) {
            $instructor = $instructors[$index % $instructors->count()];

            $course = Course::create(array_merge($courseData, [
                'academic_year' => '2025-2026',
                'status' => CourseStatus::Published,
                'created_by' => $instructor->id,
            ]));

            for ($s = 1; $s <= 2; $s++) {
                $section = CourseSection::create([
                    'course_id' => $course->id,
                    'section_name' => 'Section '.chr(64 + $s),
                    'block_code' => 'BSCS-2'.chr(64 + $s),
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

                    // Each student submits (~20% are late)
                    foreach ($sectionStudents as $student) {
                        $isLate = rand(1, 100) <= 20;
                        $submittedAt = $isLate
                            ? $assignment->due_at->copy()->addHours(rand(1, 72))
                            : now()->subHours(rand(1, 48));

                        Submission::create([
                            'assignment_id' => $assignment->id,
                            'student_id' => $student->id,
                            'status' => SubmissionStatus::Submitted,
                            'submitted_at' => $submittedAt,
                            'is_late' => $isLate,
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
