<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Compute the same throttle key that LoginRequest uses.
 */
function loginThrottleKey(string $email, string $ip = '127.0.0.1'): string
{
    return Str::transliterate(Str::lower($email).'|'.$ip);
}

test('login is rate limited after 5 failed attempts', function (): void {
    $email = 'throttle-test@example.com';
    RateLimiter::clear(loginThrottleKey($email));

    for ($i = 0; $i < 5; $i++) {
        $this->post(route('login'), [
            'email' => $email,
            'password' => 'wrong-password',
        ]);
    }

    // The 6th attempt must be throttled — session error contains the throttle message
    $response = $this->post(route('login'), [
        'email' => $email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors(['email']);
    expect(
        str_contains(session('errors')->first('email'), 'Too many') ||
        str_contains(session('errors')->first('email'), 'seconds')
    )->toBeTrue();
});

test('rate limit does not trigger before 5 failed attempts', function (): void {
    $email = 'no-throttle@example.com';
    RateLimiter::clear(loginThrottleKey($email));

    for ($i = 0; $i < 4; $i++) {
        $this->post(route('login'), [
            'email' => $email,
            'password' => 'wrong-password',
        ]);
    }

    $response = $this->post(route('login'), [
        'email' => $email,
        'password' => 'wrong-password',
    ]);

    // 5th attempt: still the standard "credentials failed" error, not a throttle error
    $response->assertSessionHasErrors(['email']);
    expect(session('errors')->first('email'))->not->toContain('Too many');
});

test('rate limit clears after a successful login', function (): void {
    $user = User::factory()->create(['email' => 'reset-limit@example.com']);
    $email = $user->email;
    RateLimiter::clear(loginThrottleKey($email));

    // Accumulate 3 failed attempts
    for ($i = 0; $i < 3; $i++) {
        $this->post(route('login'), [
            'email' => $email,
            'password' => 'wrong-password',
        ]);
    }

    // Successful login clears the counter
    $this->post(route('login'), [
        'email' => $email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    expect(RateLimiter::tooManyAttempts(loginThrottleKey($email), 5))->toBeFalse();
});
