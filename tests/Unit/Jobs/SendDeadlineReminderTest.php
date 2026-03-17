<?php

declare(strict_types=1);

use App\Jobs\SendDeadlineReminder;
use Illuminate\Contracts\Queue\ShouldQueue;

it('can be instantiated', function () {
    expect(SendDeadlineReminder::class)->toBeString();
});

it('implements ShouldQueue', function () {
    $interfaces = class_implements(SendDeadlineReminder::class);
    expect($interfaces)->toContain(ShouldQueue::class);
});
