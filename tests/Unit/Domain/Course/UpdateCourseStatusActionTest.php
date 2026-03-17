<?php

declare(strict_types=1);

use App\Domain\Audit\Actions\LogAction;
use App\Domain\Course\Actions\UpdateCourseStatus;
use App\Domain\Course\Models\Course;
use App\Enums\UserRole;
use App\Models\User;
use App\Support\Enums\CourseStatus;

it('updates course status and writes audit log', function () {
    $logAction = Mockery::mock(LogAction::class);
    $logAction->shouldReceive('execute')->once();

    $action = new UpdateCourseStatus($logAction);

    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $course = Course::create([
        'code' => 'CRS301',
        'title' => 'Status Update Course',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => CourseStatus::Draft,
        'created_by' => $admin->id,
    ]);

    $action->execute($course, CourseStatus::Published, $admin->id);

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'status' => 'published',
    ]);
});
