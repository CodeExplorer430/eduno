<?php

declare(strict_types=1);

namespace App\Domain\Notification\Actions;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetUserNotifications
{
    public function execute(): LengthAwarePaginator
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return $user->notifications()->paginate(15);
    }
}
