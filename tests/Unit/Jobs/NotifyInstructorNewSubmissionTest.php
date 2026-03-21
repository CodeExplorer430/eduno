<?php

declare(strict_types=1);

use App\Jobs\NotifyInstructorNewSubmission;
use Illuminate\Contracts\Queue\ShouldQueue;

it('can be instantiated', function () {
    expect(NotifyInstructorNewSubmission::class)->toBeString();
});

it('implements ShouldQueue', function () {
    $interfaces = class_implements(NotifyInstructorNewSubmission::class);
    expect($interfaces)->toContain(ShouldQueue::class);
});
