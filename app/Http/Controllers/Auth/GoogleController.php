<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Domain\Auth\Actions\FindOrCreateGoogleUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        /** @var \Symfony\Component\HttpFoundation\RedirectResponse $response */
        $response = Socialite::driver('google')->redirect();

        return redirect($response->getTargetUrl());
    }

    public function callback(FindOrCreateGoogleUser $action): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();
        $user = $action->handle($googleUser);

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }
}
