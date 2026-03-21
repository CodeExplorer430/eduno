<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Content\Models\Lesson;
use App\Domain\Content\Models\Module;
use App\Domain\Content\Models\Resource;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Domain\Submission\Models\Submission;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('private');
});

it('enrolled student can view submission create form', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'SUB101',
        'title' => 'Submission Create Course',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'A',
        'instructor_id' => $instructor->id,
    ]);

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Test Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);

    $response = $this->actingAs($student)->get(route('student.submissions.create', $assignment));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('Student/Submissions/Create'));
});

it('non-enrolled student cannot view submission create form', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'SUB102',
        'title' => 'Submission Block Course',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'A',
        'instructor_id' => $instructor->id,
    ]);

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Test Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    // No enrollment

    $response = $this->actingAs($student)->get(route('student.submissions.create', $assignment));

    $response->assertStatus(403);
});

it('enrolled student can view their submission', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'SUB103',
        'title' => 'Submission Show Course',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'A',
        'instructor_id' => $instructor->id,
    ]);

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Test Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);

    $submission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => 'submitted',
        'submitted_at' => now(),
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    $response = $this->actingAs($student)->get(route('student.submissions.show', $submission));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('Student/Submissions/Show'));
});

it('student cannot view another students submission', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $owner = User::factory()->create(['role' => UserRole::Student]);
    $other = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'SUB104',
        'title' => 'Submission Privacy Course',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'A',
        'instructor_id' => $instructor->id,
    ]);

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Test Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    $submission = Submission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $owner->id,
        'status' => 'submitted',
        'submitted_at' => now(),
        'is_late' => false,
        'attempt_no' => 1,
    ]);

    $response = $this->actingAs($other)->get(route('student.submissions.show', $submission));

    $response->assertStatus(403);
});

it('enrolled student can download a resource', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'SUB105',
        'title' => 'Resource Download Course',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'A',
        'instructor_id' => $instructor->id,
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);

    $module = Module::create([
        'course_section_id' => $section->id,
        'title' => 'Module 1',
        'order_no' => 1,
        'published_at' => now(),
    ]);

    $lesson = Lesson::create([
        'module_id' => $module->id,
        'title' => 'Lesson 1',
        'type' => 'document',
        'order_no' => 1,
        'published_at' => now(),
    ]);

    $fakePath = 'resources/submission-test-file.pdf';
    Storage::disk('private')->put($fakePath, 'fake pdf content');

    $resource = Resource::create([
        'lesson_id' => $lesson->id,
        'title' => 'Course Material',
        'file_path' => $fakePath,
        'mime_type' => 'application/pdf',
        'size_bytes' => 100,
        'visibility' => 'enrolled',
    ]);

    $response = $this->actingAs($student)->get(route('student.resources.download', $resource));

    $response->assertStatus(200);
});
