<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('private');
});

it('student can submit a file for an assignment', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'CS101',
        'title' => 'Test Course',
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
        'allow_resubmission' => true,
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
    ]);

    $file = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');

    $response = $this->actingAs($student)->post(
        route('student.submissions.store', $assignment),
        ['files' => [$file]]
    );

    $response->assertRedirect();
    $this->assertDatabaseHas('submissions', [
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'status' => 'submitted',
    ]);
});

it('rejects files with invalid MIME type', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'CS102',
        'title' => 'Test Course 2',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'B',
        'instructor_id' => $instructor->id,
    ]);

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Test Assignment 2',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
    ]);

    $file = UploadedFile::fake()->create('malicious.exe', 100, 'application/octet-stream');

    $response = $this->actingAs($student)->post(
        route('student.submissions.store', $assignment),
        ['files' => [$file]]
    );

    $response->assertSessionHasErrors('files.0');
});

it('guest cannot submit a file', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'CS103',
        'title' => 'Test Course 3',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'C',
        'instructor_id' => $instructor->id,
    ]);

    $assignment = Assignment::create([
        'course_section_id' => $section->id,
        'title' => 'Test Assignment 3',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    $file = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');

    $response = $this->post(
        route('student.submissions.store', $assignment),
        ['files' => [$file]]
    );

    $response->assertRedirect(route('login'));
});
