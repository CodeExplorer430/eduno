<?php

declare(strict_types=1);

namespace App\Domain\Announcement\Actions;

use App\Domain\Announcement\Models\Announcement;

class PublishAnnouncement
{
    public function handle(Announcement $announcement): Announcement
    {
        $announcement->update([
            'published_at' => $announcement->published_at === null ? now() : null,
        ]);

        return $announcement;
    }
}
