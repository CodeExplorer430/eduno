<?php

declare(strict_types=1);

namespace App\Domain\Accessibility\Actions;

use App\Domain\Accessibility\Models\UserPreference;
use App\Models\User;

class UpdatePreferences
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(User $user, array $data): UserPreference
    {
        return UserPreference::updateOrCreate(['user_id' => $user->id], $data);
    }
}
