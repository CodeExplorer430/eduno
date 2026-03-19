<?php

declare(strict_types=1);

namespace App\Domain\Accessibility\Actions;

use App\Domain\Accessibility\Models\UserPreference;
use App\Models\User;

class UpdateUserPreferences
{
    /**
     * @param  array{reduced_motion?: bool, high_contrast?: bool, dyslexia_font?: bool, font_size?: string}  $data
     */
    public function execute(User $user, array $data): UserPreference
    {
        return UserPreference::updateOrCreate(
            ['user_id' => $user->id],
            $data,
        );
    }
}
