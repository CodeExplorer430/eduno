<?php

declare(strict_types=1);

namespace App\Domain\Announcement\Actions;

use App\Domain\Announcement\Models\Announcement;
use App\Jobs\SendAnnouncementNotification;

class PublishAnnouncement
{
    public function handle(Announcement $announcement): Announcement
    {
        $wasUnpublished = $announcement->published_at === null;

        $announcement->update([
            'published_at' => $wasUnpublished ? now() : null,
        ]);

        if ($wasUnpublished) {
            $announcement->refresh();
            SendAnnouncementNotification::dispatch($announcement);
        }

        return $announcement;
    }
}
