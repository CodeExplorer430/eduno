<?php

declare(strict_types=1);

use App\Jobs\SendAnnouncementNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

it('can be instantiated', function () {
    expect(SendAnnouncementNotification::class)->toBeString();
});

it('implements ShouldQueue', function () {
    $interfaces = class_implements(SendAnnouncementNotification::class);
    expect($interfaces)->toContain(ShouldQueue::class);
});
