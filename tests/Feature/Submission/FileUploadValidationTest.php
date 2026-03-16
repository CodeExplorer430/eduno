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

it('rejects files exceeding 10MB', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'CS301',
        'title' => 'File Upload Test Course',
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
        'title' => 'Upload Test Assignment',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
    ]);

    // 11MB file exceeds the 10MB limit
    $oversizedFile = UploadedFile::fake()->create('large.pdf', 11264, 'application/pdf');

    $response = $this->actingAs($student)->post(
        route('student.submissions.store', $assignment),
        ['files' => [$oversizedFile]]
    );

    $response->assertSessionHasErrors('files.0');
});

it('accepts valid pdf and docx files', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'CS302',
        'title' => 'File Upload Test Course 2',
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
        'title' => 'Upload Test Assignment 2',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
    ]);

    $pdfFile = UploadedFile::fake()->create('essay.pdf', 500, 'application/pdf');

    $response = $this->actingAs($student)->post(
        route('student.submissions.store', $assignment),
        ['files' => [$pdfFile]]
    );

    $response->assertRedirect();
});

it('rejects disallowed mime types like exe and php', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $course = Course::create([
        'code' => 'CS303',
        'title' => 'File Upload Test Course 3',
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
        'title' => 'Upload Test Assignment 3',
        'max_score' => 100,
        'allow_resubmission' => false,
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
    ]);

    $exeFile = UploadedFile::fake()->create('virus.exe', 100, 'application/octet-stream');
    $phpFile = UploadedFile::fake()->create('shell.php', 100, 'application/x-php');

    $exeResponse = $this->actingAs($student)->post(
        route('student.submissions.store', $assignment),
        ['files' => [$exeFile]]
    );
    $exeResponse->assertSessionHasErrors('files.0');

    $phpResponse = $this->actingAs($student)->post(
        route('student.submissions.store', $assignment),
        ['files' => [$phpFile]]
    );
    $phpResponse->assertSessionHasErrors('files.0');
});
