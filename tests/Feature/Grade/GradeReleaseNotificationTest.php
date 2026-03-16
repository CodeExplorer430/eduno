<?php

declare(strict_types=1);

use App\Jobs\NotifyStudentGradeReleased;
use Illuminate\Contracts\Queue\ShouldQueue;

it('grade release job implements ShouldQueue', function () {
    $interfaces = class_implements(NotifyStudentGradeReleased::class);
    expect($interfaces)->toContain(ShouldQueue::class);
});
