<?php

declare(strict_types=1);

use App\Domain\Audit\Actions\LogAction;
use App\Domain\Course\Actions\CreateCourse;
use App\Domain\Course\Models\Course;
use App\Models\User;

it('creates a course and returns it', function () {
    $logAction = Mockery::mock(LogAction::class);
    $logAction->shouldReceive('execute')->once();

    $action = new CreateCourse($logAction);
    $creator = User::factory()->create(['role' => 'instructor']);
    $data = [
        'code' => 'CS101',
        'title' => 'Intro to CS',
        'department' => 'CS',
        'term' => 'First',
        'academic_year' => '2026-2027',
    ];

    $course = $action->execute($creator, $data);

    expect($course)->toBeInstanceOf(Course::class)
        ->and($course->code)->toBe('CS101');
});
