<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

/**
 * OAuth sign-in. Apple is gated by config services.apple.sign_in_enabled (env APPLE_SIGN_IN_ENABLED);
 * keep routes + Socialite registration so enabling Apple later is only .env + Apple Developer setup.
 */
class SocialAuthController extends Controller
{
    private const PROVIDERS = ['google', 'apple'];

    public function redirect(Request $request, string $provider): RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
    {
        $provider = strtolower($provider);
        abort_unless(in_array($provider, self::PROVIDERS, true), 404);
        abort_unless(self::providerConfigured($provider, $request->getHost()), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        $provider = strtolower($provider);
        abort_unless(in_array($provider, self::PROVIDERS, true), 404);
        abort_unless(self::providerConfigured($provider, $request->getHost()), 404);

        try {
            $oauthUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('home')
                ->with('social_login_error', __('We could not complete sign-in. Please try again or use email and password.'));
        }

        $providerId = (string) $oauthUser->getId();
        $email = $oauthUser->getEmail() ? strtolower(trim((string) $oauthUser->getEmail())) : null;
        $name = trim((string) ($oauthUser->getName() ?: $oauthUser->getNickname() ?: 'Reader'));

        $existingLink = SocialAccount::query()
            ->where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();

        if ($existingLink) {
            Auth::login($existingLink->user, true);

            return redirect()->intended(route('dashboard', absolute: false));
        }

        if ($email) {
            $user = User::query()->where('email', $email)->first();
            if ($user) {
                DB::transaction(function () use ($user, $provider, $providerId): void {
                    SocialAccount::query()->firstOrCreate(
                        [
                            'provider' => $provider,
                            'provider_id' => $providerId,
                        ],
                        [
                            'user_id' => $user->id,
                        ]
                    );
                });
                Auth::login($user, true);

                return redirect()->intended(route('dashboard', absolute: false));
            }
        }

        if (! $email) {
            return redirect()->route('home')
                ->with('social_login_error', __('We could not read an email address from your Apple account (this can happen on repeat visits). Use the same sign-in method as before, or contact support.'));
        }

        $user = DB::transaction(function () use ($email, $name, $provider, $providerId) {
            $user = User::query()->create([
                'name' => $name !== '' ? $name : 'Reader',
                'email' => $email,
                'password' => null,
            ]);
            $user->forceFill(['email_verified_at' => now()])->save();

            SocialAccount::query()->create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_id' => $providerId,
            ]);

            return $user->fresh();
        });

        event(new Registered($user));

        Auth::login($user, true);

        return redirect()->route('dashboard');
    }

    public static function providerConfigured(string $provider, ?string $requestHost = null): bool
    {
        return match ($provider) {
            'google' => self::googleConfigured($requestHost),
            'apple' => (bool) config('services.apple.sign_in_enabled', false) && self::appleConfigured($requestHost),
            default => false,
        };
    }

    private static function googleConfigured(?string $requestHost = null): bool
    {
        $id = trim((string) (config('services.google.client_id') ?? ''));
        $secret = trim((string) (config('services.google.client_secret') ?? ''));
        $redirect = trim((string) (config('services.google.redirect') ?? ''));
        if ($id === '' || $secret === '' || $redirect === '') {
            return false;
        }

        if (! self::redirectHostMatchesRequest($redirect, $requestHost)) {
            return false;
        }

        return true;
    }

    private static function appleConfigured(?string $requestHost = null): bool
    {
        $c = config('services.apple', []);
        $clientId = trim((string) ($c['client_id'] ?? ''));
        $redirect = trim((string) ($c['redirect'] ?? ''));
        if ($clientId === '' || $redirect === '') {
            return false;
        }
        if (! self::redirectHostMatchesRequest($redirect, $requestHost)) {
            return false;
        }
        if (! empty($c['client_secret'])) {
            return true;
        }

        return filled($c['team_id'] ?? null)
            && filled($c['key_id'] ?? null)
            && filled($c['private_key'] ?? null);
    }

    private static function redirectHostMatchesRequest(string $redirect, ?string $requestHost): bool
    {
        if (! is_string($requestHost) || trim($requestHost) === '') {
            return true;
        }

        $redirectHost = strtolower((string) parse_url($redirect, PHP_URL_HOST));
        $currentHost = strtolower(trim($requestHost));
        if ($redirectHost === '' || $currentHost === '') {
            return true;
        }

        if ($redirectHost === $currentHost) {
            return true;
        }

        // Same site with or without leading www (e.g. apex vs www) so OAuth buttons show on both URLs.
        $stripWww = static function (string $host): string {
            return str_starts_with($host, 'www.') ? substr($host, 4) : $host;
        };

        return $stripWww($redirectHost) === $stripWww($currentHost);
    }
}
