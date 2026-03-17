<?php

declare(strict_types=1);

use App\Domain\Audit\Actions\LogAction;
use App\Domain\Course\Actions\UpdateCourse;
use App\Domain\Course\Models\Course;
use App\Enums\UserRole;
use App\Models\User;

it('updates course fields and writes audit log', function () {
    $logAction = Mockery::mock(LogAction::class);
    $logAction->shouldReceive('execute')->once();

    $action = new UpdateCourse($logAction);

    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $course = Course::create([
        'code' => 'CRS201',
        'title' => 'Original Title',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);

    $updated = $action->execute($instructor, $course, [
        'code' => 'CRS201',
        'title' => 'Updated Title',
        'department' => 'CS',
        'term' => '1st',
        'academic_year' => '2025-2026',
    ]);

    expect($updated)->toBeInstanceOf(Course::class);
    expect($updated->title)->toBe('Updated Title');

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'title' => 'Updated Title',
    ]);
});
