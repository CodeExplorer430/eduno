<?php

declare(strict_types=1);

use App\Jobs\NotifyStudentGradeReleased;
use Illuminate\Contracts\Queue\ShouldQueue;

it('can be instantiated', function () {
    expect(NotifyStudentGradeReleased::class)->toBeString();
});

it('uses the queue', function () {
    $interfaces = class_implements(NotifyStudentGradeReleased::class);
    expect($interfaces)->toContain(ShouldQueue::class);
});
