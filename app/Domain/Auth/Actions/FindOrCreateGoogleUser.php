<?php

declare(strict_types=1);

namespace App\Domain\Auth\Actions;

use App\Enums\UserRole;
use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;

final class FindOrCreateGoogleUser
{
    public function handle(SocialiteUser $googleUser): User
    {
        /** @var User $user */
        $user = User::firstOrCreate(
            ['google_id' => $googleUser->getId()],
            [
                'name'              => $googleUser->getName() ?? $googleUser->getEmail(),
                'email'             => $googleUser->getEmail(),
                'google_id'         => $googleUser->getId(),
                'email_verified_at' => now(),
                'role'              => UserRole::Student,
            ]
        );

        return $user;
    }
}
