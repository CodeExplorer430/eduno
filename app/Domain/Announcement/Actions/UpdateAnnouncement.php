<?php

declare(strict_types=1);

namespace App\Domain\Announcement\Actions;

use App\Domain\Announcement\Models\Announcement;

class UpdateAnnouncement
{
    public function handle(Announcement $announcement, array $data): Announcement
    {
        $announcement->update([
            'title' => $data['title'],
            'body' => $data['body'],
        ]);

        return $announcement;
    }
}
