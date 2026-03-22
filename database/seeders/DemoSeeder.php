<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Accessibility\Models\UserPreference;
use App\Domain\Announcement\Models\Announcement;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Audit\Models\AuditLog;
use App\Domain\Module\Models\Lesson;
use App\Domain\Module\Models\Module;
use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\User\Models\InstructorProfile;
use App\Domain\User\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            // ----------------------------------------------------------------
            // Users
            // ----------------------------------------------------------------

            $admin = User::firstOrCreate(
                ['email' => 'admin@eduno.test'],
                [
                    'name'              => 'Admin User',
                    'password'          => Hash::make('password'),
                    'role'              => 'admin',
                    'email_verified_at' => now(),
                ],
            );

            $maria = User::firstOrCreate(
                ['email' => 'maria@eduno.test'],
                [
                    'name'              => 'Maria Santos',
                    'password'          => Hash::make('password'),
                    'role'              => 'instructor',
                    'email_verified_at' => now(),
                ],
            );

            $jose = User::firstOrCreate(
                ['email' => 'jose@eduno.test'],
                [
                    'name'              => 'Jose Reyes',
                    'password'          => Hash::make('password'),
                    'role'              => 'instructor',
                    'email_verified_at' => now(),
                ],
            );

            $juan = User::firstOrCreate(
                ['email' => 'juan@eduno.test'],
                [
                    'name'              => 'Juan dela Cruz',
                    'password'          => Hash::make('password'),
                    'role'              => 'student',
                    'email_verified_at' => now(),
                ],
            );

            $ana = User::firstOrCreate(
                ['email' => 'ana@eduno.test'],
                [
                    'name'              => 'Ana Reyes',
                    'password'          => Hash::make('password'),
                    'role'              => 'student',
                    'email_verified_at' => now(),
                ],
            );

            $carlo = User::firstOrCreate(
                ['email' => 'carlo@eduno.test'],
                [
                    'name'              => 'Carlo Santos',
                    'password'          => Hash::make('password'),
                    'role'              => 'student',
                    'email_verified_at' => now(),
                ],
            );

            $bianca = User::firstOrCreate(
                ['email' => 'bianca@eduno.test'],
                [
                    'name'              => 'Bianca Cruz',
                    'password'          => Hash::make('password'),
                    'role'              => 'student',
                    'email_verified_at' => now(),
                ],
            );

            $marco = User::firstOrCreate(
                ['email' => 'marco@eduno.test'],
                [
                    'name'              => 'Marco Lim',
                    'password'          => Hash::make('password'),
                    'role'              => 'student',
                    'email_verified_at' => now(),
                ],
            );

            $sofia = User::firstOrCreate(
                ['email' => 'sofia@eduno.test'],
                [
                    'name'              => 'Sofia Garcia',
                    'password'          => Hash::make('password'),
                    'role'              => 'student',
                    'email_verified_at' => now(),
                ],
            );

            // ----------------------------------------------------------------
            // Profiles
            // ----------------------------------------------------------------

            InstructorProfile::firstOrCreate(
                ['user_id' => $maria->id],
                ['department' => 'CCS', 'employee_number' => 'EMP-2018-001'],
            );

            InstructorProfile::firstOrCreate(
                ['user_id' => $jose->id],
                ['department' => 'CCS', 'employee_number' => 'EMP-2019-002'],
            );

            $studentProfiles = [
                [$juan,   '2023-00001', 'BSCS', 2, 'A'],
                [$ana,    '2023-00002', 'BSCS', 2, 'A'],
                [$carlo,  '2023-00003', 'BSIT', 3, 'A'],
                [$bianca, '2023-00004', 'BSIT', 2, 'B'],
                [$marco,  '2023-00005', 'BSCS', 4, 'A'],
                [$sofia,  '2023-00006', 'BSCS', 3, 'B'],
            ];

            foreach ($studentProfiles as [$student, $number, $program, $year, $section]) {
                StudentProfile::firstOrCreate(
                    ['user_id' => $student->id],
                    [
                        'student_number' => $number,
                        'program'        => $program,
                        'year_level'     => $year,
                        'section'        => $section,
                    ],
                );
            }

            // ----------------------------------------------------------------
            // Accessibility preferences (Juan and Ana)
            // ----------------------------------------------------------------

            UserPreference::firstOrCreate(
                ['user_id' => $juan->id],
                [
                    'font_size'          => 'large',
                    'high_contrast'      => true,
                    'reduced_motion'     => false,
                    'dyslexia_font'      => false,
                    'simplified_layout'  => false,
                    'language'           => 'en',
                ],
            );

            UserPreference::firstOrCreate(
                ['user_id' => $ana->id],
                [
                    'font_size'          => 'large',
                    'high_contrast'      => true,
                    'reduced_motion'     => false,
                    'dyslexia_font'      => false,
                    'simplified_layout'  => false,
                    'language'           => 'en',
                ],
            );

            // ----------------------------------------------------------------
            // Courses
            // ----------------------------------------------------------------

            $ccs123 = Course::firstOrCreate(
                ['code' => 'CCS 123'],
                [
                    'title'         => 'Introduction to Human-Computer Interaction',
                    'description'   => 'A study of the design, evaluation, and implementation of interactive computing systems for human use.',
                    'department'    => 'CCS',
                    'term'          => '2nd Semester',
                    'academic_year' => '2025-2026',
                    'status'        => 'published',
                    'created_by'    => $maria->id,
                ],
            );

            $ccs221 = Course::firstOrCreate(
                ['code' => 'CCS 221'],
                [
                    'title'         => 'Web Systems and Technologies',
                    'description'   => 'Covers the design and development of web-based applications using modern frameworks and standards.',
                    'department'    => 'CCS',
                    'term'          => '2nd Semester',
                    'academic_year' => '2025-2026',
                    'status'        => 'published',
                    'created_by'    => $jose->id,
                ],
            );

            $ccs311 = Course::firstOrCreate(
                ['code' => 'CCS 311'],
                [
                    'title'         => 'Data Structures and Algorithms',
                    'description'   => 'An in-depth study of fundamental data structures and algorithm design techniques.',
                    'department'    => 'CCS',
                    'term'          => '2nd Semester',
                    'academic_year' => '2025-2026',
                    'status'        => 'published',
                    'created_by'    => $maria->id,
                ],
            );

            // ----------------------------------------------------------------
            // Sections
            // ----------------------------------------------------------------

            $ccs123A = CourseSection::firstOrCreate(
                ['course_id' => $ccs123->id, 'block_code' => 'CCS123-A'],
                [
                    'section_name'  => 'Block A',
                    'instructor_id' => $maria->id,
                    'schedule_text' => 'Mon/Wed 9:00–10:30 AM',
                ],
            );

            $ccs123B = CourseSection::firstOrCreate(
                ['course_id' => $ccs123->id, 'block_code' => 'CCS123-B'],
                [
                    'section_name'  => 'Block B',
                    'instructor_id' => $maria->id,
                    'schedule_text' => 'Tue/Thu 9:00–10:30 AM',
                ],
            );

            $ccs221A = CourseSection::firstOrCreate(
                ['course_id' => $ccs221->id, 'block_code' => 'CCS221-A'],
                [
                    'section_name'  => 'Block A',
                    'instructor_id' => $jose->id,
                    'schedule_text' => 'Mon/Wed 1:00–2:30 PM',
                ],
            );

            $ccs221B = CourseSection::firstOrCreate(
                ['course_id' => $ccs221->id, 'block_code' => 'CCS221-B'],
                [
                    'section_name'  => 'Block B',
                    'instructor_id' => $jose->id,
                    'schedule_text' => 'Tue/Thu 1:00–2:30 PM',
                ],
            );

            $ccs311A = CourseSection::firstOrCreate(
                ['course_id' => $ccs311->id, 'block_code' => 'CCS311-A'],
                [
                    'section_name'  => 'Block A',
                    'instructor_id' => $maria->id,
                    'schedule_text' => 'Mon/Wed 3:00–4:30 PM',
                ],
            );

            $ccs311B = CourseSection::firstOrCreate(
                ['course_id' => $ccs311->id, 'block_code' => 'CCS311-B'],
                [
                    'section_name'  => 'Block B',
                    'instructor_id' => $maria->id,
                    'schedule_text' => 'Tue/Thu 3:00–4:30 PM',
                ],
            );

            // ----------------------------------------------------------------
            // Enrollments
            // ----------------------------------------------------------------

            // CCS 123 Block A: Juan, Ana, Carlo, Marco
            foreach ([$juan, $ana, $carlo, $marco] as $student) {
                Enrollment::firstOrCreate(
                    ['user_id' => $student->id, 'course_section_id' => $ccs123A->id],
                    ['status' => 'active', 'enrolled_at' => now()->subWeeks(3)],
                );
            }

            // CCS 123 Block B: Bianca, Sofia
            foreach ([$bianca, $sofia] as $student) {
                Enrollment::firstOrCreate(
                    ['user_id' => $student->id, 'course_section_id' => $ccs123B->id],
                    ['status' => 'active', 'enrolled_at' => now()->subWeeks(3)],
                );
            }

            // CCS 221 Block A: Bianca, Sofia, Carlo (cross-enrolled)
            foreach ([$bianca, $sofia, $carlo] as $student) {
                Enrollment::firstOrCreate(
                    ['user_id' => $student->id, 'course_section_id' => $ccs221A->id],
                    ['status' => 'active', 'enrolled_at' => now()->subWeeks(3)],
                );
            }

            // CCS 311 Block A: Juan, Ana, Carlo, Marco
            foreach ([$juan, $ana, $carlo, $marco] as $student) {
                Enrollment::firstOrCreate(
                    ['user_id' => $student->id, 'course_section_id' => $ccs311A->id],
                    ['status' => 'active', 'enrolled_at' => now()->subWeeks(3)],
                );
            }

            // ----------------------------------------------------------------
            // Modules & Lessons — CCS 123 Block A
            // ----------------------------------------------------------------

            $mod1_123A = Module::firstOrCreate(
                ['course_section_id' => $ccs123A->id, 'order_no' => 1],
                [
                    'title'        => 'HCI Foundations',
                    'description'  => 'Introduction to the field of Human-Computer Interaction and its core principles.',
                    'published_at' => now()->subWeeks(3),
                ],
            );

            Lesson::firstOrCreate(
                ['module_id' => $mod1_123A->id, 'order_no' => 1],
                [
                    'title'        => 'What is HCI?',
                    'content'      => "Human-Computer Interaction (HCI) is a multidisciplinary field that focuses on the design and use of computer technology, with emphasis on the interfaces between users and computers.\n\nHCI researchers study how people interact with computers and design technologies that let humans interact with computers in novel ways. It spans across computer science, behavioral sciences, design, and media studies.",
                    'type'         => 'text',
                    'published_at' => now()->subWeeks(3),
                ],
            );

            Lesson::firstOrCreate(
                ['module_id' => $mod1_123A->id, 'order_no' => 2],
                [
                    'title'        => 'Usability Principles',
                    'content'      => "Usability is a quality attribute that assesses how easy user interfaces are to use. Jakob Nielsen's 10 usability heuristics are widely used guidelines:\n\n1. Visibility of system status\n2. Match between system and the real world\n3. User control and freedom\n4. Consistency and standards\n5. Error prevention\n6. Recognition rather than recall\n7. Flexibility and efficiency of use\n8. Aesthetic and minimalist design\n9. Help users recognize, diagnose, and recover from errors\n10. Help and documentation",
                    'type'         => 'text',
                    'published_at' => now()->subWeeks(3),
                ],
            );

            $mod2_123A = Module::firstOrCreate(
                ['course_section_id' => $ccs123A->id, 'order_no' => 2],
                [
                    'title'        => 'Interaction Design',
                    'description'  => 'Practical methods and tools for designing effective user interactions.',
                    'published_at' => now()->subWeeks(2),
                ],
            );

            Lesson::firstOrCreate(
                ['module_id' => $mod2_123A->id, 'order_no' => 1],
                [
                    'title'        => 'Prototyping Methods',
                    'content'      => "Prototyping is an essential part of the design process. There are several fidelity levels:\n\n**Low-fidelity prototypes** (paper sketches, wireframes) are quick to produce and easy to discard. They are best used in early-stage ideation.\n\n**Mid-fidelity prototypes** (clickable wireframes) demonstrate user flows without full visual design.\n\n**High-fidelity prototypes** (interactive mockups) closely resemble the final product and are used for usability testing.",
                    'type'         => 'text',
                    'published_at' => now()->subWeeks(2),
                ],
            );

            Lesson::firstOrCreate(
                ['module_id' => $mod2_123A->id, 'order_no' => 2],
                [
                    'title'        => 'Figma Tutorial',
                    'content'      => 'https://www.figma.com/resources/learn-design/',
                    'type'         => 'link',
                    'published_at' => now()->subWeeks(2),
                ],
            );

            // Modules & Lessons — CCS 123 Block B (mirrors Block A content)
            $mod1_123B = Module::firstOrCreate(
                ['course_section_id' => $ccs123B->id, 'order_no' => 1],
                [
                    'title'        => 'HCI Foundations',
                    'description'  => 'Introduction to the field of Human-Computer Interaction and its core principles.',
                    'published_at' => now()->subWeeks(3),
                ],
            );

            Lesson::firstOrCreate(
                ['module_id' => $mod1_123B->id, 'order_no' => 1],
                [
                    'title'        => 'What is HCI?',
                    'content'      => "Human-Computer Interaction (HCI) is a multidisciplinary field that focuses on the design and use of computer technology, with emphasis on the interfaces between users and computers.\n\nHCI researchers study how people interact with computers and design technologies that let humans interact with computers in novel ways. It spans across computer science, behavioral sciences, design, and media studies.",
                    'type'         => 'text',
                    'published_at' => now()->subWeeks(3),
                ],
            );

            Lesson::firstOrCreate(
                ['module_id' => $mod1_123B->id, 'order_no' => 2],
                [
                    'title'        => 'Usability Principles',
                    'content'      => "Usability is a quality attribute that assesses how easy user interfaces are to use. Jakob Nielsen's 10 usability heuristics are widely used guidelines:\n\n1. Visibility of system status\n2. Match between system and the real world\n3. User control and freedom\n4. Consistency and standards\n5. Error prevention\n6. Recognition rather than recall\n7. Flexibility and efficiency of use\n8. Aesthetic and minimalist design\n9. Help users recognize, diagnose, and recover from errors\n10. Help and documentation",
                    'type'         => 'text',
                    'published_at' => now()->subWeeks(3),
                ],
            );

            $mod2_123B = Module::firstOrCreate(
                ['course_section_id' => $ccs123B->id, 'order_no' => 2],
                [
                    'title'        => 'Interaction Design',
                    'description'  => 'Practical methods and tools for designing effective user interactions.',
                    'published_at' => now()->subWeeks(2),
                ],
            );

            Lesson::firstOrCreate(
                ['module_id' => $mod2_123B->id, 'order_no' => 1],
                [
                    'title'        => 'Prototyping Methods',
                    'content'      => "Prototyping is an essential part of the design process. There are several fidelity levels:\n\n**Low-fidelity prototypes** (paper sketches, wireframes) are quick to produce and easy to discard. They are best used in early-stage ideation.\n\n**Mid-fidelity prototypes** (clickable wireframes) demonstrate user flows without full visual design.\n\n**High-fidelity prototypes** (interactive mockups) closely resemble the final product and are used for usability testing.",
                    'type'         => 'text',
                    'published_at' => now()->subWeeks(2),
                ],
            );

            Lesson::firstOrCreate(
                ['module_id' => $mod2_123B->id, 'order_no' => 2],
                [
                    'title'        => 'Figma Tutorial',
                    'content'      => 'https://www.figma.com/resources/learn-design/',
                    'type'         => 'link',
                    'published_at' => now()->subWeeks(2),
                ],
            );

            // Modules & Lessons — CCS 221 Block A
            $mod1_221A = Module::firstOrCreate(
                ['course_section_id' => $ccs221A->id, 'order_no' => 1],
                [
                    'title'        => 'Web Fundamentals',
                    'description'  => 'Core concepts of the web: HTTP, HTML, CSS, and JavaScript.',
                    'published_at' => now()->subWeeks(3),
                ],
            );

            Lesson::firstOrCreate(
                ['module_id' => $mod1_221A->id, 'order_no' => 1],
                [
                    'title'        => 'How the Web Works',
                    'content'      => "The World Wide Web operates on a client-server model. A browser (client) sends an HTTP request to a web server. The server processes the request and returns an HTTP response containing HTML, CSS, and JavaScript.\n\nKey protocols: HTTP (Hypertext Transfer Protocol), HTTPS (secure HTTP with TLS encryption), DNS (Domain Name System for resolving domain names to IP addresses).",
                    'type'         => 'text',
                    'published_at' => now()->subWeeks(3),
                ],
            );

            Lesson::firstOrCreate(
                ['module_id' => $mod1_221A->id, 'order_no' => 2],
                [
                    'title'        => 'Semantic HTML',
                    'content'      => "Semantic HTML uses elements that convey meaning about their content. Examples include `<header>`, `<nav>`, `<main>`, `<article>`, `<section>`, `<aside>`, and `<footer>`.\n\nUsing semantic HTML improves accessibility for assistive technologies such as screen readers, and helps search engines understand page structure.",
                    'type'         => 'text',
                    'published_at' => now()->subWeeks(3),
                ],
            );

            $mod2_221A = Module::firstOrCreate(
                ['course_section_id' => $ccs221A->id, 'order_no' => 2],
                [
                    'title'        => 'Modern Web Frameworks',
                    'description'  => 'Introduction to popular web frameworks and their ecosystems.',
                    'published_at' => now()->subWeeks(2),
                ],
            );

            Lesson::firstOrCreate(
                ['module_id' => $mod2_221A->id, 'order_no' => 1],
                [
                    'title'        => 'Introduction to Vue.js',
                    'content'      => "Vue.js is a progressive JavaScript framework for building user interfaces. It uses a component-based architecture where the UI is broken into reusable, self-contained pieces.\n\nVue 3 introduced the Composition API, which allows logic to be organized by feature rather than by option type, improving code reuse and readability.",
                    'type'         => 'text',
                    'published_at' => now()->subWeeks(2),
                ],
            );

            Lesson::firstOrCreate(
                ['module_id' => $mod2_221A->id, 'order_no' => 2],
                [
                    'title'        => 'Vue.js Official Documentation',
                    'content'      => 'https://vuejs.org/guide/introduction.html',
                    'type'         => 'link',
                    'published_at' => now()->subWeeks(2),
                ],
            );

            // Modules & Lessons — CCS 311 Block A
            $mod1_311A = Module::firstOrCreate(
                ['course_section_id' => $ccs311A->id, 'order_no' => 1],
                [
                    'title'        => 'Arrays and Linked Lists',
                    'description'  => 'Fundamental linear data structures and their trade-offs.',
                    'published_at' => now()->subWeeks(3),
                ],
            );

            Lesson::firstOrCreate(
                ['module_id' => $mod1_311A->id, 'order_no' => 1],
                [
                    'title'        => 'Array Operations and Complexity',
                    'content'      => "Arrays store elements in contiguous memory locations. Random access is O(1) because the element address is computed directly. Insertion and deletion are O(n) because elements must be shifted.\n\nDynamic arrays (e.g., ArrayList in Java, list in Python) resize automatically by allocating a new block of memory (typically double the current capacity) when the array is full. Amortized insertion is O(1).",
                    'type'         => 'text',
                    'published_at' => now()->subWeeks(3),
                ],
            );

            Lesson::firstOrCreate(
                ['module_id' => $mod1_311A->id, 'order_no' => 2],
                [
                    'title'        => 'Singly and Doubly Linked Lists',
                    'content'      => "A linked list is a linear data structure where each element (node) contains a value and a pointer to the next node. Singly linked lists allow traversal in one direction only.\n\nDoubly linked lists store both next and previous pointers, enabling O(1) removal of a node given its reference. The trade-off is higher memory usage.\n\nCommon operations: insertion at head/tail O(1), search O(n), deletion given reference O(1) for doubly linked.",
                    'type'         => 'text',
                    'published_at' => now()->subWeeks(3),
                ],
            );

            $mod2_311A = Module::firstOrCreate(
                ['course_section_id' => $ccs311A->id, 'order_no' => 2],
                [
                    'title'        => 'Sorting Algorithms',
                    'description'  => 'Classic sorting algorithms, their complexity, and practical usage.',
                    'published_at' => now()->subWeeks(2),
                ],
            );

            Lesson::firstOrCreate(
                ['module_id' => $mod2_311A->id, 'order_no' => 1],
                [
                    'title'        => 'Comparison-Based Sorting',
                    'content'      => "Comparison-based sorting algorithms determine order by comparing pairs of elements.\n\n- **Bubble Sort:** O(n²) time, O(1) space. Simple but slow.\n- **Insertion Sort:** O(n²) worst, O(n) best (nearly sorted). Good for small or nearly sorted data.\n- **Merge Sort:** O(n log n) always, O(n) space. Stable sort, good for linked lists.\n- **Quick Sort:** O(n log n) average, O(n²) worst. In-place, cache-friendly, widely used in practice.",
                    'type'         => 'text',
                    'published_at' => now()->subWeeks(2),
                ],
            );

            Lesson::firstOrCreate(
                ['module_id' => $mod2_311A->id, 'order_no' => 2],
                [
                    'title'        => 'Visualgo — Algorithm Visualizations',
                    'content'      => 'https://visualgo.net/en/sorting',
                    'type'         => 'link',
                    'published_at' => now()->subWeeks(2),
                ],
            );

            // Stub modules for remaining sections (CCS 221 Block B, CCS 311 Block B)
            foreach ([$ccs221B, $ccs311B] as $section) {
                $stubMod1 = Module::firstOrCreate(
                    ['course_section_id' => $section->id, 'order_no' => 1],
                    [
                        'title'        => 'Module 1',
                        'description'  => 'Core concepts and foundations.',
                        'published_at' => now()->subWeeks(3),
                    ],
                );

                Lesson::firstOrCreate(
                    ['module_id' => $stubMod1->id, 'order_no' => 1],
                    [
                        'title'        => 'Introduction',
                        'content'      => 'Welcome to this module. Please review the reading materials provided.',
                        'type'         => 'text',
                        'published_at' => now()->subWeeks(3),
                    ],
                );

                Lesson::firstOrCreate(
                    ['module_id' => $stubMod1->id, 'order_no' => 2],
                    [
                        'title'        => 'Key Concepts',
                        'content'      => 'This lesson covers the key concepts for this section of the course.',
                        'type'         => 'text',
                        'published_at' => now()->subWeeks(3),
                    ],
                );

                $stubMod2 = Module::firstOrCreate(
                    ['course_section_id' => $section->id, 'order_no' => 2],
                    [
                        'title'        => 'Module 2',
                        'description'  => 'Applied exercises and assessments.',
                        'published_at' => now()->subWeeks(2),
                    ],
                );

                Lesson::firstOrCreate(
                    ['module_id' => $stubMod2->id, 'order_no' => 1],
                    [
                        'title'        => 'Practical Application',
                        'content'      => 'Apply what you have learned by completing the activities in this lesson.',
                        'type'         => 'text',
                        'published_at' => now()->subWeeks(2),
                    ],
                );

                Lesson::firstOrCreate(
                    ['module_id' => $stubMod2->id, 'order_no' => 2],
                    [
                        'title'        => 'Further Reading',
                        'content'      => 'https://developer.mozilla.org/en-US/docs/Learn',
                        'type'         => 'link',
                        'published_at' => now()->subWeeks(2),
                    ],
                );
            }

            // ----------------------------------------------------------------
            // Assignments — CCS 123 Block A (primary demo section)
            // ----------------------------------------------------------------

            $assign1_123A = Assignment::firstOrCreate(
                ['course_section_id' => $ccs123A->id, 'title' => 'Needs Assessment Report'],
                [
                    'instructions'        => "Conduct a needs assessment for a real or hypothetical target user group. Your report should include:\n\n1. A description of the target users and their context\n2. Identified user needs and pain points\n3. A task analysis of at least one key user workflow\n4. Recommendations for system features\n\nSubmit as a PDF or DOCX file, maximum 10 pages.",
                    'due_at'              => now()->subWeeks(2),
                    'max_score'           => 100,
                    'allow_resubmission'  => false,
                    'allowed_file_types'  => ['pdf', 'docx'],
                    'published_at'        => now()->subWeeks(4),
                ],
            );

            $assign2_123A = Assignment::firstOrCreate(
                ['course_section_id' => $ccs123A->id, 'title' => 'Prototype Wireframes'],
                [
                    'instructions'        => "Based on your needs assessment, create low-to-mid fidelity wireframes for your proposed system. Submit a PDF of your wireframes and/or exported PNG screenshots. You may also include your Figma project as a ZIP export.\n\nEnsure your wireframes include at least 5 distinct screens and demonstrate a complete user flow.",
                    'due_at'              => now()->subDays(3),
                    'max_score'           => 100,
                    'allow_resubmission'  => false,
                    'allowed_file_types'  => ['pdf', 'png', 'zip'],
                    'published_at'        => now()->subWeeks(3),
                ],
            );

            Assignment::firstOrCreate(
                ['course_section_id' => $ccs123A->id, 'title' => 'Final Usability Report'],
                [
                    'instructions'        => "Conduct a formal usability evaluation of your prototype. Your report must include:\n\n1. Methodology (heuristic evaluation or user testing)\n2. Participant descriptions (if user testing)\n3. Findings organized by Nielsen's heuristics\n4. Severity ratings for each issue found\n5. Recommendations for redesign\n\nSubmit as a PDF or DOCX file.",
                    'due_at'              => now()->addDays(7),
                    'max_score'           => 100,
                    'allow_resubmission'  => true,
                    'allowed_file_types'  => ['pdf', 'docx'],
                    'published_at'        => now()->subDays(1),
                ],
            );

            // Assignments — CCS 311 Block A
            $assign1_311A = Assignment::firstOrCreate(
                ['course_section_id' => $ccs311A->id, 'title' => 'Array Problem Set'],
                [
                    'instructions'        => "Complete the following array and linked list problems. Show your solutions with code and explain the time and space complexity of each algorithm.\n\nSubmit your solutions as a single PDF document.",
                    'due_at'              => now()->subWeeks(2),
                    'max_score'           => 100,
                    'allow_resubmission'  => false,
                    'allowed_file_types'  => ['pdf', 'docx'],
                    'published_at'        => now()->subWeeks(4),
                ],
            );

            Assignment::firstOrCreate(
                ['course_section_id' => $ccs311A->id, 'title' => 'Sorting Algorithm Implementation'],
                [
                    'instructions'        => "Implement merge sort and quick sort in the language of your choice. Include unit tests and a brief analysis comparing the two algorithms on different input sizes.\n\nSubmit as a ZIP file containing your source code and a PDF write-up.",
                    'due_at'              => now()->subDays(3),
                    'max_score'           => 100,
                    'allow_resubmission'  => false,
                    'allowed_file_types'  => ['pdf', 'zip'],
                    'published_at'        => now()->subWeeks(3),
                ],
            );

            Assignment::firstOrCreate(
                ['course_section_id' => $ccs311A->id, 'title' => 'Algorithm Analysis Report'],
                [
                    'instructions'        => "Write a comparative analysis of at least three sorting or searching algorithms. Include Big-O analysis, best/worst/average cases, and real-world use cases for each.\n\nSubmit as a PDF document, minimum 5 pages.",
                    'due_at'              => now()->addDays(7),
                    'max_score'           => 100,
                    'allow_resubmission'  => true,
                    'allowed_file_types'  => ['pdf', 'docx'],
                    'published_at'        => now()->subDays(1),
                ],
            );

            // Assignments — CCS 221 Block A
            Assignment::firstOrCreate(
                ['course_section_id' => $ccs221A->id, 'title' => 'HTML & CSS Portfolio Page'],
                [
                    'instructions'        => "Build a personal portfolio page using semantic HTML5 and CSS. Requirements: responsive layout, navigation, about section, and a project gallery. Validate your HTML with the W3C validator.\n\nSubmit as a ZIP file of your project folder.",
                    'due_at'              => now()->subWeeks(2),
                    'max_score'           => 100,
                    'allow_resubmission'  => false,
                    'allowed_file_types'  => ['zip'],
                    'published_at'        => now()->subWeeks(4),
                ],
            );

            Assignment::firstOrCreate(
                ['course_section_id' => $ccs221A->id, 'title' => 'Vue.js Component Library'],
                [
                    'instructions'        => "Build a small Vue.js component library with at least 5 reusable components (e.g., Button, Card, Modal, Form Input, Alert). Include Storybook or a simple demo page.\n\nSubmit as a ZIP file.",
                    'due_at'              => now()->subDays(3),
                    'max_score'           => 100,
                    'allow_resubmission'  => false,
                    'allowed_file_types'  => ['zip'],
                    'published_at'        => now()->subWeeks(3),
                ],
            );

            Assignment::firstOrCreate(
                ['course_section_id' => $ccs221A->id, 'title' => 'Full-Stack Web Application'],
                [
                    'instructions'        => "Build and deploy a full-stack web application with a RESTful API backend and a Vue.js frontend. Include authentication, CRUD operations on at least one resource, and a brief README.\n\nSubmit as a ZIP file and include the live URL if deployed.",
                    'due_at'              => now()->addDays(7),
                    'max_score'           => 100,
                    'allow_resubmission'  => true,
                    'allowed_file_types'  => ['zip', 'pdf'],
                    'published_at'        => now()->subDays(1),
                ],
            );

            // ----------------------------------------------------------------
            // Submissions — Assignment 1, CCS 123 Block A (graded & released)
            // ----------------------------------------------------------------

            $sub_juan_a1 = Submission::firstOrCreate(
                ['assignment_id' => $assign1_123A->id, 'student_id' => $juan->id],
                [
                    'status'       => 'graded',
                    'submitted_at' => now()->subWeeks(2)->subDay(),
                    'is_late'      => false,
                    'attempt_no'   => 1,
                ],
            );

            $sub_ana_a1 = Submission::firstOrCreate(
                ['assignment_id' => $assign1_123A->id, 'student_id' => $ana->id],
                [
                    'status'       => 'graded',
                    'submitted_at' => now()->subWeeks(2)->addDays(2),
                    'is_late'      => true,
                    'attempt_no'   => 1,
                ],
            );

            $sub_carlo_a1 = Submission::firstOrCreate(
                ['assignment_id' => $assign1_123A->id, 'student_id' => $carlo->id],
                [
                    'status'       => 'graded',
                    'submitted_at' => now()->subWeeks(2)->subHours(3),
                    'is_late'      => false,
                    'attempt_no'   => 1,
                ],
            );

            $sub_marco_a1 = Submission::firstOrCreate(
                ['assignment_id' => $assign1_123A->id, 'student_id' => $marco->id],
                [
                    'status'       => 'graded',
                    'submitted_at' => now()->subWeeks(2)->subHours(6),
                    'is_late'      => false,
                    'attempt_no'   => 1,
                ],
            );

            // ----------------------------------------------------------------
            // Submissions — Assignment 2, CCS 123 Block A (graded, not released)
            // ----------------------------------------------------------------

            $sub_juan_a2 = Submission::firstOrCreate(
                ['assignment_id' => $assign2_123A->id, 'student_id' => $juan->id],
                [
                    'status'       => 'graded',
                    'submitted_at' => now()->subDays(4),
                    'is_late'      => false,
                    'attempt_no'   => 1,
                ],
            );

            $sub_carlo_a2 = Submission::firstOrCreate(
                ['assignment_id' => $assign2_123A->id, 'student_id' => $carlo->id],
                [
                    'status'       => 'graded',
                    'submitted_at' => now()->subDays(4)->subHours(2),
                    'is_late'      => false,
                    'attempt_no'   => 1,
                ],
            );

            // Submissions — Assignment 1, CCS 311 Block A
            $sub_juan_311a1 = Submission::firstOrCreate(
                ['assignment_id' => $assign1_311A->id, 'student_id' => $juan->id],
                [
                    'status'       => 'graded',
                    'submitted_at' => now()->subWeeks(2)->subDay(),
                    'is_late'      => false,
                    'attempt_no'   => 1,
                ],
            );

            $sub_ana_311a1 = Submission::firstOrCreate(
                ['assignment_id' => $assign1_311A->id, 'student_id' => $ana->id],
                [
                    'status'       => 'graded',
                    'submitted_at' => now()->subWeeks(2)->subHours(5),
                    'is_late'      => false,
                    'attempt_no'   => 1,
                ],
            );

            $sub_carlo_311a1 = Submission::firstOrCreate(
                ['assignment_id' => $assign1_311A->id, 'student_id' => $carlo->id],
                [
                    'status'       => 'graded',
                    'submitted_at' => now()->subWeeks(2)->subHours(2),
                    'is_late'      => false,
                    'attempt_no'   => 1,
                ],
            );

            $sub_marco_311a1 = Submission::firstOrCreate(
                ['assignment_id' => $assign1_311A->id, 'student_id' => $marco->id],
                [
                    'status'       => 'graded',
                    'submitted_at' => now()->subWeeks(2)->subHours(1),
                    'is_late'      => false,
                    'attempt_no'   => 1,
                ],
            );

            // ----------------------------------------------------------------
            // Grades — Assignment 1, CCS 123 Block A (released)
            // ----------------------------------------------------------------

            $releasedAt = now()->subWeeks(1);

            $grade_juan_a1 = Grade::firstOrCreate(
                ['submission_id' => $sub_juan_a1->id],
                [
                    'graded_by'   => $maria->id,
                    'score'       => 88.00,
                    'feedback'    => 'Good analysis of user needs.',
                    'released_at' => $releasedAt,
                ],
            );

            $grade_ana_a1 = Grade::firstOrCreate(
                ['submission_id' => $sub_ana_a1->id],
                [
                    'graded_by'   => $maria->id,
                    'score'       => 72.00,
                    'feedback'    => 'Late submission; content is satisfactory.',
                    'released_at' => $releasedAt,
                ],
            );

            $grade_carlo_a1 = Grade::firstOrCreate(
                ['submission_id' => $sub_carlo_a1->id],
                [
                    'graded_by'   => $maria->id,
                    'score'       => 91.00,
                    'feedback'    => 'Excellent depth of research.',
                    'released_at' => $releasedAt,
                ],
            );

            $grade_marco_a1 = Grade::firstOrCreate(
                ['submission_id' => $sub_marco_a1->id],
                [
                    'graded_by'   => $maria->id,
                    'score'       => 85.00,
                    'feedback'    => 'Well-structured report.',
                    'released_at' => $releasedAt,
                ],
            );

            // ----------------------------------------------------------------
            // Grades — Assignment 2, CCS 123 Block A (not released)
            // ----------------------------------------------------------------

            $grade_juan_a2 = Grade::firstOrCreate(
                ['submission_id' => $sub_juan_a2->id],
                [
                    'graded_by'   => $maria->id,
                    'score'       => 90.00,
                    'feedback'    => 'Strong prototype work.',
                    'released_at' => null,
                ],
            );

            $grade_carlo_a2 = Grade::firstOrCreate(
                ['submission_id' => $sub_carlo_a2->id],
                [
                    'graded_by'   => $maria->id,
                    'score'       => 78.00,
                    'feedback'    => 'Wireframes need more detail.',
                    'released_at' => null,
                ],
            );

            // Grades — Assignment 1, CCS 311 Block A (released)
            $grade_juan_311a1 = Grade::firstOrCreate(
                ['submission_id' => $sub_juan_311a1->id],
                [
                    'graded_by'   => $maria->id,
                    'score'       => 90.00,
                    'feedback'    => 'Correct solutions with clear complexity analysis.',
                    'released_at' => $releasedAt,
                ],
            );

            $grade_ana_311a1 = Grade::firstOrCreate(
                ['submission_id' => $sub_ana_311a1->id],
                [
                    'graded_by'   => $maria->id,
                    'score'       => 83.00,
                    'feedback'    => 'Good work. Review edge cases for linked list removal.',
                    'released_at' => $releasedAt,
                ],
            );

            $grade_carlo_311a1 = Grade::firstOrCreate(
                ['submission_id' => $sub_carlo_311a1->id],
                [
                    'graded_by'   => $maria->id,
                    'score'       => 95.00,
                    'feedback'    => 'Outstanding. All solutions are optimal.',
                    'released_at' => $releasedAt,
                ],
            );

            $grade_marco_311a1 = Grade::firstOrCreate(
                ['submission_id' => $sub_marco_311a1->id],
                [
                    'graded_by'   => $maria->id,
                    'score'       => 80.00,
                    'feedback'    => 'Solid understanding demonstrated.',
                    'released_at' => $releasedAt,
                ],
            );

            // ----------------------------------------------------------------
            // Audit Logs
            // ----------------------------------------------------------------

            $auditEntries = [
                // CCS 123 Block A — Assignment 1 grades created
                ['actor_id' => $maria->id, 'action' => 'grade.created', 'entity_type' => 'Grade', 'entity_id' => $grade_juan_a1->id,   'metadata' => ['score' => 88, 'submission_id' => $sub_juan_a1->id]],
                ['actor_id' => $maria->id, 'action' => 'grade.created', 'entity_type' => 'Grade', 'entity_id' => $grade_ana_a1->id,    'metadata' => ['score' => 72, 'submission_id' => $sub_ana_a1->id]],
                ['actor_id' => $maria->id, 'action' => 'grade.created', 'entity_type' => 'Grade', 'entity_id' => $grade_carlo_a1->id,  'metadata' => ['score' => 91, 'submission_id' => $sub_carlo_a1->id]],
                ['actor_id' => $maria->id, 'action' => 'grade.created', 'entity_type' => 'Grade', 'entity_id' => $grade_marco_a1->id,  'metadata' => ['score' => 85, 'submission_id' => $sub_marco_a1->id]],
                // CCS 123 Block A — Assignment 1 grades released
                ['actor_id' => $maria->id, 'action' => 'grade.released', 'entity_type' => 'Grade', 'entity_id' => $grade_juan_a1->id,  'metadata' => ['released_at' => $releasedAt->toIso8601String()]],
                ['actor_id' => $maria->id, 'action' => 'grade.released', 'entity_type' => 'Grade', 'entity_id' => $grade_ana_a1->id,   'metadata' => ['released_at' => $releasedAt->toIso8601String()]],
                ['actor_id' => $maria->id, 'action' => 'grade.released', 'entity_type' => 'Grade', 'entity_id' => $grade_carlo_a1->id, 'metadata' => ['released_at' => $releasedAt->toIso8601String()]],
                ['actor_id' => $maria->id, 'action' => 'grade.released', 'entity_type' => 'Grade', 'entity_id' => $grade_marco_a1->id, 'metadata' => ['released_at' => $releasedAt->toIso8601String()]],
                // CCS 123 Block A — Assignment 2 grades created (not released)
                ['actor_id' => $maria->id, 'action' => 'grade.created', 'entity_type' => 'Grade', 'entity_id' => $grade_juan_a2->id,   'metadata' => ['score' => 90, 'submission_id' => $sub_juan_a2->id]],
                ['actor_id' => $maria->id, 'action' => 'grade.created', 'entity_type' => 'Grade', 'entity_id' => $grade_carlo_a2->id,  'metadata' => ['score' => 78, 'submission_id' => $sub_carlo_a2->id]],
                // CCS 311 Block A — Assignment 1 grades created & released
                ['actor_id' => $maria->id, 'action' => 'grade.created',  'entity_type' => 'Grade', 'entity_id' => $grade_juan_311a1->id,  'metadata' => ['score' => 90, 'submission_id' => $sub_juan_311a1->id]],
                ['actor_id' => $maria->id, 'action' => 'grade.created',  'entity_type' => 'Grade', 'entity_id' => $grade_ana_311a1->id,   'metadata' => ['score' => 83, 'submission_id' => $sub_ana_311a1->id]],
                ['actor_id' => $maria->id, 'action' => 'grade.created',  'entity_type' => 'Grade', 'entity_id' => $grade_carlo_311a1->id, 'metadata' => ['score' => 95, 'submission_id' => $sub_carlo_311a1->id]],
                ['actor_id' => $maria->id, 'action' => 'grade.created',  'entity_type' => 'Grade', 'entity_id' => $grade_marco_311a1->id, 'metadata' => ['score' => 80, 'submission_id' => $sub_marco_311a1->id]],
                ['actor_id' => $maria->id, 'action' => 'grade.released', 'entity_type' => 'Grade', 'entity_id' => $grade_juan_311a1->id,  'metadata' => ['released_at' => $releasedAt->toIso8601String()]],
                ['actor_id' => $maria->id, 'action' => 'grade.released', 'entity_type' => 'Grade', 'entity_id' => $grade_ana_311a1->id,   'metadata' => ['released_at' => $releasedAt->toIso8601String()]],
                ['actor_id' => $maria->id, 'action' => 'grade.released', 'entity_type' => 'Grade', 'entity_id' => $grade_carlo_311a1->id, 'metadata' => ['released_at' => $releasedAt->toIso8601String()]],
                ['actor_id' => $maria->id, 'action' => 'grade.released', 'entity_type' => 'Grade', 'entity_id' => $grade_marco_311a1->id, 'metadata' => ['released_at' => $releasedAt->toIso8601String()]],
            ];

            foreach ($auditEntries as $entry) {
                AuditLog::create(array_merge($entry, ['created_at' => now()]));
            }

            // ----------------------------------------------------------------
            // Announcements
            // ----------------------------------------------------------------

            // CCS 123 Block A
            Announcement::firstOrCreate(
                ['course_section_id' => $ccs123A->id, 'title' => 'Welcome to CCS 123!'],
                [
                    'body'         => "Welcome to Introduction to Human-Computer Interaction! I'm excited to have you all in this class.\n\nPlease review the course syllabus posted in the Files section and come prepared to discuss what you already know about HCI in our first synchronous session.",
                    'created_by'   => $maria->id,
                    'published_at' => now()->subWeeks(3),
                ],
            );

            Announcement::firstOrCreate(
                ['course_section_id' => $ccs123A->id, 'title' => 'Assignment 1 Graded — Check Your Feedback'],
                [
                    'body'         => "Assignment 1 (Needs Assessment Report) has been graded and feedback has been released. Please log in to the Grades section to view your score and detailed comments.\n\nIf you have questions about your grade, please reach out during office hours or send me a message.",
                    'created_by'   => $maria->id,
                    'published_at' => now()->subWeeks(1),
                ],
            );

            Announcement::firstOrCreate(
                ['course_section_id' => $ccs123A->id, 'title' => 'Final Project Details Released'],
                [
                    'body'         => "The Final Usability Report assignment is now available. Please read the instructions carefully — you will be conducting a formal usability evaluation of your prototype from Assignment 2.\n\nThe deadline is one week from today. Resubmissions are allowed up to the deadline.",
                    'created_by'   => $maria->id,
                    'published_at' => now()->subDays(1),
                ],
            );

            // CCS 123 Block B
            Announcement::firstOrCreate(
                ['course_section_id' => $ccs123B->id, 'title' => 'Welcome to CCS 123!'],
                [
                    'body'         => "Welcome to Introduction to Human-Computer Interaction (Block B)! Please review the course syllabus and the Module 1 readings before our first class session.",
                    'created_by'   => $maria->id,
                    'published_at' => now()->subWeeks(3),
                ],
            );

            Announcement::firstOrCreate(
                ['course_section_id' => $ccs123B->id, 'title' => 'Reminder: Assignment 1 Due Soon'],
                [
                    'body'         => "This is a reminder that the Needs Assessment Report is due in two weeks. Please start early and reach out if you need help with the requirements.",
                    'created_by'   => $maria->id,
                    'published_at' => now()->subWeeks(2)->subDays(3),
                ],
            );

            // CCS 221 Block A
            Announcement::firstOrCreate(
                ['course_section_id' => $ccs221A->id, 'title' => 'Welcome to CCS 221!'],
                [
                    'body'         => "Welcome to Web Systems and Technologies! This semester we will cover the full web development stack from HTML/CSS fundamentals to building full-stack applications with Vue.js and Laravel.\n\nMake sure you have Node.js, PHP, and a code editor installed before our first lab session.",
                    'created_by'   => $jose->id,
                    'published_at' => now()->subWeeks(3),
                ],
            );

            Announcement::firstOrCreate(
                ['course_section_id' => $ccs221A->id, 'title' => 'Lab Environment Setup Guide'],
                [
                    'body'         => "Please follow the setup guide in Module 1 to configure your development environment. We will begin hands-on labs next week and you should have everything ready.\n\nIf you run into issues, post in the class group chat and a classmate or I will help you.",
                    'created_by'   => $jose->id,
                    'published_at' => now()->subWeeks(2),
                ],
            );

            Announcement::firstOrCreate(
                ['course_section_id' => $ccs221A->id, 'title' => 'Assignment 1 Graded'],
                [
                    'body'         => "The HTML & CSS Portfolio assignment has been graded. Check your scores in the Grades section. Overall the class did well — pay attention to semantic HTML and responsive design in future assignments.",
                    'created_by'   => $jose->id,
                    'published_at' => now()->subWeeks(1),
                ],
            );

            // CCS 311 Block A
            Announcement::firstOrCreate(
                ['course_section_id' => $ccs311A->id, 'title' => 'Welcome to CCS 311!'],
                [
                    'body'         => "Welcome to Data Structures and Algorithms! This is one of the most important courses in your CS education — the concepts here underpin nearly every software system you will build or maintain.\n\nBe prepared to think deeply, practice consistently, and ask questions.",
                    'created_by'   => $maria->id,
                    'published_at' => now()->subWeeks(3),
                ],
            );

            Announcement::firstOrCreate(
                ['course_section_id' => $ccs311A->id, 'title' => 'Array Problem Set Graded'],
                [
                    'body'         => "Assignment 1 (Array Problem Set) has been graded. Feedback is now visible in the Grades section. Several students had issues with Big-O analysis — we will revisit this in the next synchronous session.",
                    'created_by'   => $maria->id,
                    'published_at' => now()->subWeeks(1),
                ],
            );

            Announcement::firstOrCreate(
                ['course_section_id' => $ccs311A->id, 'title' => 'Algorithm Analysis Report — Final Assessment'],
                [
                    'body'         => "The final assessment (Algorithm Analysis Report) is now live. This is your opportunity to demonstrate your understanding of algorithm complexity. Take your time, be thorough, and cite your sources.\n\nDue date: one week from today.",
                    'created_by'   => $maria->id,
                    'published_at' => now()->subDays(1),
                ],
            );

            // ----------------------------------------------------------------
            // In-app Notifications (database channel demo data)
            // ----------------------------------------------------------------
            // Delete existing seeded notifications so re-seeding is idempotent
            DB::table('notifications')
                ->whereIn('notifiable_id', [$juan->id, $ana->id, $maria->id])
                ->delete();

            $now = now();

            DB::table('notifications')->insert([
                // Juan — unread (3 total, badge = 3)
                [
                    'id'              => (string) Str::uuid(),
                    'type'            => 'App\\Notifications\\GradeReleasedNotification',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id'   => $juan->id,
                    'data'            => json_encode([
                        'message' => "Your grade for \"Needs Assessment Report\" has been released: 88/100",
                        'url'     => url('/student/submissions'),
                        'type'    => 'grade_released',
                    ]),
                    'read_at'    => null,
                    'created_at' => $now->copy()->subHours(2),
                    'updated_at' => $now->copy()->subHours(2),
                ],
                [
                    'id'              => (string) Str::uuid(),
                    'type'            => 'App\\Notifications\\AnnouncementPublishedNotification',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id'   => $juan->id,
                    'data'            => json_encode([
                        'message' => "New announcement in CCS 123: \"Assignment 1 Graded — Check Your Feedback\"",
                        'url'     => url('/student/announcements'),
                        'type'    => 'announcement_published',
                    ]),
                    'read_at'    => null,
                    'created_at' => $now->copy()->subHours(5),
                    'updated_at' => $now->copy()->subHours(5),
                ],
                [
                    'id'              => (string) Str::uuid(),
                    'type'            => 'App\\Notifications\\DeadlineReminderNotification',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id'   => $juan->id,
                    'data'            => json_encode([
                        'message' => "Reminder: \"Final Usability Report\" is due in 7 days",
                        'url'     => url('/student/assignments'),
                        'type'    => 'deadline_reminder',
                    ]),
                    'read_at'    => null,
                    'created_at' => $now->copy()->subDay(),
                    'updated_at' => $now->copy()->subDay(),
                ],
                // Ana — all read
                [
                    'id'              => (string) Str::uuid(),
                    'type'            => 'App\\Notifications\\GradeReleasedNotification',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id'   => $ana->id,
                    'data'            => json_encode([
                        'message' => "Your grade for \"Needs Assessment Report\" has been released: 72/100",
                        'url'     => url('/student/submissions'),
                        'type'    => 'grade_released',
                    ]),
                    'read_at'    => $now->copy()->subHours(1),
                    'created_at' => $now->copy()->subHours(3),
                    'updated_at' => $now->copy()->subHours(1),
                ],
                [
                    'id'              => (string) Str::uuid(),
                    'type'            => 'App\\Notifications\\AnnouncementPublishedNotification',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id'   => $ana->id,
                    'data'            => json_encode([
                        'message' => "New announcement in CCS 123: \"Welcome to CCS 123!\"",
                        'url'     => url('/student/announcements'),
                        'type'    => 'announcement_published',
                    ]),
                    'read_at'    => $now->copy()->subHours(2),
                    'created_at' => $now->copy()->subWeek(),
                    'updated_at' => $now->copy()->subHours(2),
                ],
                // Maria (instructor)
                [
                    'id'              => (string) Str::uuid(),
                    'type'            => 'App\\Notifications\\NewSubmissionNotification',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id'   => $maria->id,
                    'data'            => json_encode([
                        'message' => "Juan dela Cruz submitted \"Needs Assessment Report\"",
                        'url'     => url('/instructor/assignments'),
                        'type'    => 'new_submission',
                    ]),
                    'read_at'    => $now->copy()->subHours(1),
                    'created_at' => $now->copy()->subHours(4),
                    'updated_at' => $now->copy()->subHours(1),
                ],
                [
                    'id'              => (string) Str::uuid(),
                    'type'            => 'App\\Notifications\\NewSubmissionNotification',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id'   => $maria->id,
                    'data'            => json_encode([
                        'message' => "Carlo Santos submitted \"Prototype Wireframes\"",
                        'url'     => url('/instructor/assignments'),
                        'type'    => 'new_submission',
                    ]),
                    'read_at'    => null,
                    'created_at' => $now->copy()->subHours(1),
                    'updated_at' => $now->copy()->subHours(1),
                ],
            ]);
        });
    }
}
