<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user'                       => $request->user()?->only([
                    'id', 'name', 'email', 'role',
                ]),
                'unread_notifications_count' => fn () => Auth::check()
                    ? Auth::user()->unreadNotifications()->count()
                    : 0,
            ],
            'ziggy' => fn () => [
                ...(new Ziggy())->toArray(),
                'location' => $request->url(),
            ],
            'locale'       => fn () => $this->resolveLocale($request),
            'translations' => fn () => $this->loadTranslations($request),
            'userPrefs' => fn () => $request->user()?->preferences?->only([
                'reduced_motion',
                'high_contrast',
                'dyslexia_font',
                'font_size',
                'dark_mode',
                'email_notifications',
                'email_digest',
                'language',
            ]),
        ];
    }

    private function resolveLocale(Request $request): string
    {
        $language = $request->user()?->preferences?->language;

        return in_array($language, ['en', 'fil'], strict: true) ? $language : App::getLocale();
    }

    /** @return array<string, mixed> */
    private function loadTranslations(Request $request): array
    {
        $locale = $this->resolveLocale($request);
        App::setLocale($locale);

        /** @var array<string, mixed>|string $raw */
        $raw = Lang::get('app');

        return is_array($raw) ? $raw : [];
    }
}
