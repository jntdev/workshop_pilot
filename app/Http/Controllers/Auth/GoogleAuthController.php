<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuthorizedEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        $driver = Socialite::driver('google')
            ->scopes(['openid', 'email', 'profile']);

        // Désactiver la vérification SSL en développement local (Windows)
        if (app()->environment('local')) {
            $driver->setHttpClient(
                new \GuzzleHttp\Client(['verify' => false])
            );
        }

        return $driver->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $driver = Socialite::driver('google');

            // Désactiver la vérification SSL en développement local (Windows)
            if (app()->environment('local')) {
                $driver->setHttpClient(
                    new \GuzzleHttp\Client(['verify' => false])
                );
            }

            $googleUser = $driver->user();
        } catch (Throwable $exception) {
            Log::warning('Google OAuth failed.', ['error' => $exception->getMessage()]);

            return redirect()
                ->route('login')
                ->withErrors(['auth' => 'Connexion Google impossible.']);
        }

        $email = Str::lower($googleUser->getEmail() ?? '');

        if ($email === '' || ! AuthorizedEmail::where('email', $email)->exists()) {
            return redirect()
                ->route('login')
                ->withErrors(['auth' => 'Adresse e-mail non autorisee.']);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = new User;
            $user->email = $email;
            $user->name = $googleUser->getName() ?: $email;
            $user->password = Str::random(64);
        } elseif ($googleUser->getName()) {
            $user->name = $googleUser->getName();
        }

        if (! $user->email_verified_at) {
            $user->email_verified_at = now();
        }

        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(config('fortify.home'));
    }
}
