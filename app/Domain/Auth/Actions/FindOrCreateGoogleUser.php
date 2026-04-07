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
        $email    = $googleUser->getEmail();
        $googleId = $googleUser->getId();

        // If an account already exists for this email, link the Google ID and return it.
        // This prevents an attacker with a Google account from creating a shadow user
        // for an existing email-based account (OAuth account-takeover prevention).
        $existing = User::where('email', $email)->first();

        if ($existing instanceof User) {
            if (is_null($existing->google_id)) {
                $existing->google_id = $googleId;
                $existing->save();
            }

            return $existing;
        }

        return User::create([
            'name'              => $googleUser->getName() ?? $email,
            'email'             => $email,
            'google_id'         => $googleId,
            'email_verified_at' => now(),
            'role'              => UserRole::Student,
        ]);
    }
}
