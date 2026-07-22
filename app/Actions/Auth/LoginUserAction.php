<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginUserAction
{
    /**
     * Authenticate a user with email and password, protecting against brute-force attacks.
     *
     * @throws ValidationException
     */
    public function execute(string $email, string $password, bool $remember = false): User
    {
        $throttleKey = Str::transliterate(Str::lower($email) . '|' . request()->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => ["Muitas tentativas de login. Por favor, tente novamente em {$seconds} segundos."],
            ]);
        }

        if (!Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => ['As credenciais informadas não correspondem aos nossos registros.'],
            ]);
        }

        RateLimiter::clear($throttleKey);

        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        return Auth::user();
    }
}
