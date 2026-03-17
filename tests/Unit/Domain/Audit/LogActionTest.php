<?php

declare(strict_types=1);

use App\Domain\Audit\Actions\LogAction;
use App\Domain\Course\Models\Course;
use App\Enums\UserRole;
use App\Models\User;

it('creates an AuditLog record with the correct fields', function () {
    $actor = User::factory()->create(['role' => UserRole::Instructor]);

    $action = new LogAction;

    $action->execute(
        $actor->id,
        'course.created',
        Course::class,
        42,
        ['code' => 'CS101'],
    );

    $this->assertDatabaseHas('audit_logs', [
        'actor_id' => $actor->id,
        'action' => 'course.created',
        'entity_type' => Course::class,
        'entity_id' => 42,
    ]);
});

it('does not throw when audit log write fails', function () {
    $action = new LogAction;

    expect(fn () => $action->execute(
        null,
        'test.action',
        'NonExistentModel',
        null,
        null,
    ))->not->toThrow(Throwable::class);
});
